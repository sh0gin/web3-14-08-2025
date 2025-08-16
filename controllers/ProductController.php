<?php

namespace app\controllers;

use app\models\Baskets;
use app\models\Category;
use app\models\ImagesProducts;
use app\models\Orders;
use app\models\OrdersProducts;
use app\models\ProductBasket;
use app\models\Products;
use app\models\ReasonCancellation;
use app\models\StatusOrders;
use app\models\User;
use GuzzleHttp\Psr7\UploadedFile;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\UploadedFile as WebUploadedFile;

class ProductController extends ActiveController
{

    public $modelClass = '';
    public $enableCsrfValidaion = '';
    public $checkExtensionByMimeType = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // remove authentication filter
        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => [isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'http://' . $_SERVER['REMOTE_ADDR']],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
            ],
            'actions' => [
                'login' => [
                    'Access-Control-Allow-Credentials' => true,
                ]
            ],
        ];


        $auth = [
            'class' => HttpBearerAuth::class,
            'only' => ['add-category', 'add-product', 'create-basket', 'add-one-product', 'del-one-product', 'sum-for-basket', 'order', 'del-all-product', 'cancel-orders', 'get-orders', 'put-balance'],
        ];

        // re-add authentication filter
        $behaviors['authenticator'] = $auth;
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ['options'];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['create']);

        // customize the data provider preparation with the "prepareDataProvider()" method
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }

    public function actionAddProduct()
    {
        if (Yii::$app->user->identity->role_id == 2) {
            $model = new Products();
            $model->load(Yii::$app->request->post(), '');
            $model->files = WebUploadedFile::getInstancesByName('files');
            if ($model->validate()) {
                $files = [];
                $model->save();
                foreach ($model->files as $value) {
                    $model_files = new ImagesProducts();
                    $random = Yii::$app->security->generateRandomString(6);
                    $file_path = __DIR__ . "/../imageForProduct/$random-{$value->name}";
                    $value->saveAs($file_path);
                    $model_files->image = $file_path;
                    $model_files->product_id = $model->id;
                    $model_files->save(false);
                    $files[] = $file_path;
                }
                return $this->asJson([
                    'data' => [
                        'id' => $model->id,
                        'category' => $model->name,
                    ],
                    'files' => $files,
                    'code' => 200,
                    'message' => 'new product add',
                ]);
            } else {
                return $this->asJson([
                    'error' => [
                        'code' => 422,
                        'message' => "Validation Erorr",
                        'error' => $model->errors,
                    ]
                ]);
            }
        } else {
            Yii::$app->respones->statusCode = 403;
        }
    }

    public function actionAddCategory()
    {
        if (Yii::$app->user->identity->role_id == 2) {
            $model = new Category();
            $model->load(Yii::$app->request->post(), '');
            if ($model->validate()) {
                $model->save();
                return $this->asJson([
                    'data' => [
                        'id' => $model->id,
                        'category' => $model->name,
                    ],
                    'code' => 200,
                    'message' => 'new category add',
                ]);
            } else {
                return $this->asJson([
                    'error' => [
                        'code' => 422,
                        'message' => "Validation Erorr",
                        'error' => $model->errors,
                    ]
                ]);
            }
        } else {
            Yii::$app->respones->statusCode = 403;
        }
    }

    public function actionGetProducts()
    {
        $query = new Query();
        $query->select('products.id, products.name, price, quantity, category.name as category, image')
            ->from("Products")
            ->innerJoin('ImagesProducts', 'products.id=imagesproducts.product_id')
            ->innerJoin('Category', 'products.category_id=category.id');

        $provider = new ActiveDataProvider([
            "query" => $query,

        ]);

        $post = Yii::$app->request->post();
        if ($post) {
            $provider->setPagination(['pageSize' => $post['count'], 'page' => $post['page']]);
        }

        return $this->asJson([
            'data' => [
                'products' => $provider->getModels(),
                'totalCount' => $provider->totalCount,
            ],
            'code' => 200,
            'message' => 'Все продукты получены',
        ]);
    }

    public function actionCreateBasket()
    {
        $model = new Baskets();
        $model->user_id = Yii::$app->user->identity->id;
        if ($model->save()) {
            Yii::$app->response->statusCode = 201;
            return $this->asJson([
                'data' => [
                    'code' => 201,
                    'message' => 'Корзина создана',
                ]
            ]);
        } else {
            return $this->asJson([
                'error' => [
                    'code' => 422,
                    'message' => "Validation Erorr",
                    'error' => $model->errors,
                ]
            ]);
        };
    }

    public function actionAddOneProduct($id)
    {
        $basket = Baskets::findOne(['user_id' => Yii::$app->user->identity->id]);

        if ($basket) {
            $product = Products::findOne($id);
            if ($product) {

                if ($product->quantity > 0) {
                    $model = ProductBasket::findOne(['basket_id' => $basket->id, 'products_id' => $product->id]);
                    $basket->totalSum += $product->price;
                    $basket->save(false);
                    if ($model) {
                        $model->count += 1;
                        $model->totalPrice += $product->price;
                        $product->quantity -= 1;
                        $product->save();
                    } else {
                        $model = new ProductBasket();
                        $model->products_id = $id;
                        $model->basket_id = $basket->id;
                        $model->count = 1;
                        $model->totalPrice = $product->price;
                        $product->quantity -= 1;
                        $product->save();
                    }
                    if ($model->save()) {
                        return $this->asJson([
                            'data' => [
                                'product_name' => $product->name,
                                'count_product' => $model->count,
                                'total_price_for_product' => $model->totalPrice,
                            ],
                            'code' => 200,
                            'message' => 'Единица товара добавлена',
                        ]);
                    } else {
                        return $this->asJson([
                            'error' => [
                                'code' => 422,
                                'message' => "Validation Erorr",
                                'error' => $model->errors,
                            ]
                        ]);
                    }
                } else {
                    Yii::$app->response->statusCode = 403;
                }
            } else {
                Yii::$app->response->statusCode = 403;
            }
        } else {
            Yii::$app->response->statusCode = 403;
        }
    }

    public function actionDelOneProduct($id)
    {
        $basket = Baskets::findOne(['user_id' => Yii::$app->user->identity->id]);

        if ($basket) {
            $product = Products::findOne($id);
            if ($product) {
                $model = ProductBasket::findOne(['basket_id' => $basket->id, 'products_id' => $product->id]);
                if ($model) {
                    $basket->totalSum -= $product->price;
                    $basket->save(false);
                    if ($model->count == 1) {
                        $model->delete();
                        if (!ProductBasket::findOne(['basket_id' => $basket->id])) {
                            $basket->delete();
                        }


                        $product->quantity += 1;
                        $product->save();
                        return $this->asJson([
                            'data' => [
                                'product_name' => $product->name,
                                'count_product' => 0,
                                'total_price_for_product' => 0
                            ],
                            'code' => 200,
                            'message' => 'Товар удалён из корзины',
                        ]);
                    } else {
                        $model->count -= 1;
                        $model->totalPrice -= $product->price;
                        $model->save();
                        $product->quantity += 1;
                        $product->save();

                        return $this->asJson([
                            'data' => [
                                'product_name' => $product->name,
                                'count_product' => $model->count,
                                'total_price_for_product' => $model->totalPrice,
                            ],
                            'code' => 200,
                            'message' => 'Одна единица товара удалена из корзины',
                        ]);
                    }
                } else {
                    Yii::$app->response->statusCode = 403;
                }
            } else {
                Yii::$app->response->statusCode = 404;
            }
        } else {
            Yii::$app->response->statusCode = 403;
        }
    }

    public function actionSumForBasket()
    {
        $basket = Baskets::findOne(['user_id' => Yii::$app->user->identity->id]);
        $model = ProductBasket::findAll(['basket_id' => $basket->id]);

        $sum = 0;  // можно асивее сделть
        foreach ($model as $value) {
            $sum += $value->totalPrice;
        }

        $basket->totalSum = $sum;
        $basket->save();

        return $this->asJson([
            'data' => [
                'totalCount' => $basket->totalSum,
            ],
            'code' => 200,
            'message' => 'Сумма установленна',
        ]);
    }

    public function actionOrder()
    {
        $model_basket = Baskets::findOne(['user_id' => Yii::$app->user->identity->id]);
        if ($model_basket) {
            
            $model_productsBasket = ProductBasket::findAll(['basket_id' => $model_basket->id]);
            $orders = new Orders();
            $orders->user_id = Yii::$app->user->identity->id;
            
            $sum = 0;  // можно красивее сделть
            foreach ($model_productsBasket as $value) {
                $sum += $value->totalPrice;
            }
            if ($sum < Yii::$app->user->identity->balance) {
                
                
                $orders->general_price = $sum;
                $orders->track_code = Yii::$app->security->generateRandomString(12);
                $orders->status_id = 1;
                $orders->save(false);


                if ($model_productsBasket) {
                    foreach ($model_productsBasket as $value) {
                        $model_productsOrders = new OrdersProducts();
                        $model_productsOrders->products_id = $value->products_id;
                        $model_productsOrders->count = $value->count;
                        $model_productsOrders->totalPrice = $value->count;
                        $model_productsOrders->orders_id = $orders->id;
                        $model_productsOrders->save();
                    }
                    $user = User::findOne(Yii::$app->user->identity->id);
                    $user->balance -= $sum;
                    $user->save(false);

                    $model_basket->delete();
                    return $this->asJson([
                        'data' => [
                            'orders' => [
                                'track_code' => $orders->track_code,
                                'general_price' => $orders->general_price,
                                'status' => StatusOrders::getStatusName($orders->status_id),
                                'balance_now' => $user->balance,
                            ],
                            'code' => 201,
                            'message' => "Заказ оформлен"
                        ]
                    ]);
                } else {
                    Yii::$app->response->statusCode = 403;
                }
            } else {
                return $this->asJson([
                    'error' => [
                        'message' => 'Недостаточно средств',
                    ]
                ]);
            }
        } else {
            Yii::$app->response->statusCode = 403;
        }
    }

    public function actionDelAllProduct($id)
    {
        $basket_model = Baskets::findOne(['user_id' => Yii::$app->user->identity->id]);
        if ($basket_model) {
            $model = ProductBasket::findOne(['basket_id' => $basket_model->id, 'products_id' => $id]);
            if ($model) {
                $product = Products::findOne($model->products_id);
                $product->quantity += $model->count;
                $product->save();
                $model->delete();
                Yii::$app->response->statusCode = 204;
                return;
            } else {
                Yii::$app->response->statusCode = 403;
            }
        } else {
            Yii::$app->response->statusCode = 403;
        }
    }

    public function actionCancelOrders($id)
    {
        $model = Orders::findOne($id);
        if (Yii::$app->user->identity->role_id == 2) {
            if ($model) {
                if ($model->status_id == 1) {
                    $new_cancel = new ReasonCancellation();
                    $new_cancel->order_id = $id;
                    $new_cancel->text = Yii::$app->request->post()['text'];
                    if ($new_cancel->save()) {
                        $model->status_id = 3;
                        $model->save(false);

                        $model_ProcutsOrders = OrdersProducts::findAll(['orders_id' => $model->id]);
                        foreach ($model_ProcutsOrders as $value) { //возвращает товары из заказа  в quantity 
                            $products = Products::findOne($value->products_id);
                            $products->quantity += $value->count;
                            $products->save(false);
                        }

                        return $this->asJson([
                            'data' => [
                                'message' => $new_cancel->text,
                            ],
                            'code' => 201,
                            'message' => 'Заказ отменен',
                        ]);
                    } else {
                        return $this->asJson([
                            'error' => [
                                'code' => 422,
                                'message' => "Validation Erorr",
                                'error' => $model->errors,
                            ]
                        ]);
                    }
                } else {
                    Yii::$app->response->statusCode = 403;
                }
            } else {
                Yii::$app->response->statusCode = 404;
            }
        } else {
            Yii::$app->response->statusCode = 403;
        }
    }

    public function actionGetOrders()
    {
        $result = [];
        foreach (Orders::findAll(['user_id' => Yii::$app->user->identity->id]) as $value) {
            $result[] = [
                'id' => $value->id,
                'data_of_creation' => $value->data_of_creation,
                'general_price' => $value->general_price,
                'track_code' => $value->track_code,
                'status' => StatusOrders::getStatusName($value->status_id),
            ];
            if ($value->status_id == 3) {
                $result[array_key_last($result)]['message'] = ReasonCancellation::findOne(['order_id' => $value->id])->text;
            }
        }
        return $this->asJson([
            'data' => $result,
            'user' => Yii::$app->user->identity->email,
        ]);
    }

    public function actionPutBalance()
    {
        $user = User::findOne(Yii::$app->user->identity->id);
        $user->balance += Yii::$app->request->post()['money'];
        $user->save();
        Yii::$app->response->statusCode = 201;
        return;
    }
}
