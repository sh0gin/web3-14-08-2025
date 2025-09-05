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
use app\models\Users;
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
        // проверка админа, 
        // remove authentication filter
        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                // 'Origin' => [isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'http://' . $_SERVER['REMOTE_ADDR']],
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
            ],
            'actions' => [
                'add-product' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
                'add-category' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
                'create-basket' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
                'add-one-product' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
                'put-balance' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
                'get-orders' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
                'cancel-orders' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
                'del-all-product' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
                'order' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
                'sum-for-basket' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
                'del-one-product' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
            ],
        ];


        $auth = [
            'class' => HttpBearerAuth::class,
            'only' => ['add-category', 'add-product', 'create-basket', 'add-one-product', 'del-one-product', 'sum-for-basket', 'order', 'del-all-product', 'cancel-orders', 'get-orders', 'put-balance', 'get-basket'],
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
        $user = Users::findOne(Yii::$app->user->identity->id);
        if ($user->isAdmin()) {
            $model = new Products();
            $model->load(Yii::$app->request->post(), '');
            $model->files = WebUploadedFile::getInstancesByName('files');
            if ($model->validate()) {
                $files = [];
                $model->save();
                foreach ($model->files as $value) {

                    $model_files = new ImagesProducts();
                    $file = Yii::$app->security->generateRandomString(6) . "---{$value->name}";
                    $file_path = __DIR__ . "/../web/ImagesForProducts/$file";
                    $value->saveAs($file_path);
                    $model_files->image = $file;
                    $model_files->product_id = $model->id;
                    $model_files->save(false);
                    $files[] = Yii::$app->request->getHostInfo() . '/web/imagesForProducts/' . $file;
                }


                return $this->asJson([
                    'data' => [
                        'id' => $model->id,
                        'name' => $model->name,
                        'category' => $model->category->name,
                    ],
                    'files' => $files,
                    'code' => 200,
                    'message' => 'new product add',
                ]);
            } else {
                Yii::$app->response->statusCode = 422;
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
    }

    public function actionAddCategory()
    {
        $model_user = Users::findOne(Yii::$app->user->identity->id);
        if ($model_user->isAdmin()) {
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
            Yii::$app->response->statusCode = 403;
        }
    }

    public function actionGetProducts()
    {
        $query = new Query();
        $query->select('products.id, products.name, price, quantity, category.name as category')
            ->from("Products")
            ->innerJoin('Category', 'products.category_id=category.id');

        $provider = new ActiveDataProvider([
            "query" => $query,
        ]);

        if (Yii::$app->request->method == 'POST') {
            $post = Yii::$app->request->post();
            if (array_key_exists('count', $post) && array_key_exists('page', $post)) {
                $provider->setPagination(['pageSize' => $post['count'], 'page' => $post['page'] - 1]);
            } else {
                return $this->asJson([
                    'error' => [
                        'code' => 500,
                        'message' => 'Не правильные параметры',
                    ]
                ]);
            }
        }

        $result = [];
        foreach ($provider->getModels() as $value) {

            $value['file_url'] = array_map(fn($value) => $value['file_url'], ImagesProducts::find()
                ->select([
                    "CONCAT('"
                        . Yii::$app->request->getHostInfo()
                        . "/web/imagesForProducts/', image) as file_url"
                ])
                ->where(['product_id' => $value['id']])->asArray()->ALL());
            $result[] = $value;
        }

        return $this->asJson([
            'data' => [
                'products' => $result,
                'totalCount' => $provider->totalCount,
            ],
            'code' => 200,
            'message' => 'Все продукты получены',
        ]);
    }

    public function actionGetBasket()
    {
        $model_basket = Baskets::findOne(['user_id' => Yii::$app->user->identity->id]);
        if ($model_basket) {
            $basket_with_product = [];
            $totalProductCount = 0;
            foreach (ProductBasket::findAll(['basket_id' => $model_basket->id]) as $value) {

                $basket_with_product[] = [
                    'id' => $value->products_id,
                    'product_name' => Products::getProductName($value->products_id),
                    'count' => $value->count,
                    'price_for_this_product' => $value->totalPrice,
                    'price_for_once' => $value->totalPrice / $value->count,
                ];
                $totalProductCount += $value->count;
            }

            foreach ($basket_with_product as &$value) {

                $value['file_url'] = array_map(fn($value) => $value['file_url'], ImagesProducts::find()
                    ->select([
                        "CONCAT('"
                            . Yii::$app->request->getHostInfo()
                            . "/web/imagesForProducts/', image) as file_url"
                    ])
                    ->where(['product_id' => $value['id']])->asArray()->all());
            }

            return $this->asJson([
                'data' => [
                    'basket' => $basket_with_product,
                    'totalCount' => $totalProductCount,
                    'totalSum' => $model_basket->totalSum,
                ]
            ]);
        } else {
            return $this->asJson([
                'data' => [
                    'basket' => [],
                    'totalCount' => 0,
                    'totalSum' => 0,
                ]
            ]);
        }
    }

    public function actionAddOneProduct($id)
    {
        $basket = Baskets::findOne(['user_id' => Yii::$app->user->identity->id]);

        if (!$basket) {
            $basket = new Baskets();
            $basket->user_id = Yii::$app->user->identity->id;
            $basket->save(false);
        }

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

                    $count = 0;

                    $basket_with_product = [];

                    foreach (ProductBasket::findAll(['basket_id' => $basket->id]) as $value) {

                        $basket_with_product[] = [
                            'product_name' => Products::getProductName($value->products_id),
                            'count' => $value->count,
                            'price_for_this_product' => $value->totalPrice,
                        ];
                        $count += $value->count;
                    };

                    return $this->asJson([
                        'data' => [
                            'new_product' => [
                                'product_name' => $product->name,
                                'count_product' => $model->count,
                                'total_price_for_product' => $model->totalPrice,
                            ],
                            'basket' => $basket_with_product,
                            'count_products' => $count,
                            'price_for_all' => $basket->totalSum,
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
            Yii::$app->response->statusCode = 404;
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

    // public function actionSumForBasket()
    // {
    //     $basket = Baskets::findOne(['user_id' => Yii::$app->user->identity->id]);
    //     $model = ProductBasket::findAll(['basket_id' => $basket->id]);

    //     $sum = 0;  // можно rhасивее сделть
    //     foreach ($model as $value) {
    //         $sum += $value->totalPrice;
    //     }

    //     $basket->totalSum = $sum;
    //     $basket->save();

    //     return $this->asJson([
    //         'data' => [
    //             'totalCount' => $basket->totalSum,
    //         ],
    //         'code' => 200,
    //         'message' => 'Сумма установленна',
    //     ]);
    // }

    public function actionOrder()
    {
        $model_basket = Baskets::findOne(['user_id' => Yii::$app->user->identity->id]);
        if ($model_basket) {

            $model_productsBasket = ProductBasket::findAll(['basket_id' => $model_basket->id]);
            $orders = new Orders();
            $orders->user_id = Yii::$app->user->identity->id;

            $sum = 0;
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
                        $model_productsOrders->totalPrice = $value->totalPrice;
                        $model_productsOrders->orders_id = $orders->id;
                        $model_productsOrders->save();
                    }
                    $user = Users::findOne(Yii::$app->user->identity->id);
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

                $basketProduct = ProductBasket::findOne(['basket_id' => $basket_model->id]);
                if ($basketProduct) {
                    $basket_model->totalSum -= $model->totalPrice;
                    $basket_model->save();
                } else {
                    $basket_model->delete();
                }

                Yii::$app->response->statusCode = 204;
                return $this->asJson([
                    'code' => 204,
                    'message' => 'Данные удалены',
                ]);
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
        $user = Users::findOne(Yii::$app->user->identity->id);
        if ($user->isAdmin()) {
            if ($model) {
                if ($model->status_id == 1) {
                    $new_cancel = new ReasonCancellation();
                    $new_cancel->order_id = $id;
                    $new_cancel->load(Yii::$app->request->post(), '');
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
                                'error' => $new_cancel->errors,
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
            $products = [];
            foreach (OrdersProducts::findAll(['orders_id' => $value->id]) as $elem) {
                $image = ImagesProducts::findAll(['product_id' => $elem->products_id]);
                $result_image = [];
                if ($image) {
                    foreach ($image as $one_image) {
                        $result_image[] = Yii::$app->request->getHostInfo() . "/web/imagesForProducts/" . $one_image->image;
                    }
                }
                $products[] = [
                    'product' => Products::getProductName($elem->products_id),
                    'count' => $elem->count,
                    'totalPrice' => $elem->totalPrice,
                    'image' => $result_image,
                ];
            }
            $result[] = [
                'id' => $value->id,
                'data_of_creation' => $value->data_of_creation,
                'general_price' => $value->general_price,
                'products' => $products,
                'track_code' => $value->track_code,
                'status' => StatusOrders::getStatusName($value->status_id),
            ];
            if ($value->status_id == 3) {
                $result[array_key_last($result)]['message'] = ReasonCancellation::findOne(['order_id' => $value->id])->text;
            } else {
                $result[array_key_last($result)]['message'] = '';
            }
        }
        return $this->asJson([
            'data' => $result,
            'user' => Yii::$app->user->identity->email,
        ]);
    }

    public function actionPutBalance()
    {
        $user = Users::findOne(Yii::$app->user->identity->id);
        if (array_key_exists('balance', Yii::$app->request->post())) {
            $user->balance += (int) Yii::$app->request->post()['balance'];
            $user->save();
            Yii::$app->response->statusCode = 201;
            return;
        } else {
            return $this->asJson([
                'error' => [
                    'code' => 422,
                    'message' => 'Validation Error',
                ]
            ]);
        }
    }

    public function actionSearchProducts()
    {
        $model = Products::findOne(['name'  => Yii::$app->request->post()['name']]);
        if ($model) {
            return $this->asJson([
                'data' => [
                    'id' => $model->id,
                    'name'  => $model->name,
                    'category' => Category::getCategoryName($model->category_id),
                    'quantity' => $model->quantity,
                    'price' => $model->price,
                ]
            ]);
        } else {
            Yii::$app->response->statusCode = 404;
        }
    }

    public function actionGetInfoOrders($code)
    {
        $order = Orders::findOne(['track_code' => $code]);
        if ($order) {
            $user = Users::findOne($order->user_id);
            $products = [];
            foreach (OrdersProducts::findAll(['orders_id' => $order->id]) as $elem) {
                $products = [
                    'product' => Products::getProductName($elem->products_id),
                    'count' => $elem->count,
                    'totalPrice' => $elem->totalPrice,
                ];
            }
            return $this->asJson([
                'data' => [
                    'order' => [
                        'status' => StatusOrders::getStatusName($order->status_id),
                        'data_of_creation' => $order->data_of_creation,
                        'general_price' => $order->general_price,
                        'products' => $products,
                    ],
                    'user' => [
                        'first-name' => $user->first_name,
                        'last-name' => $user->last_name,
                    ],
                ]
            ]);
        } else {
            Yii::$app->response->statusCode = 404;
        }
    }
}
