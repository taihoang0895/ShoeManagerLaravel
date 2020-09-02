<?php

namespace Tests\Feature;

use App\models\DetailProduct;
use App\models\Discount;
use App\models\functions\AdminFunctions;
use App\models\CampaignName;
use App\models\functions\ResultCode;
use App\models\Product;
use App\models\ProductCategory;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use function Sodium\add;

class DBAdminFunctionsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testInsertCampaignName()
    {
        $campaignName = factory(CampaignName::class)->make();
        $resultCode = AdminFunctions::saveCampaignName($campaignName);
        $listCampaignName = AdminFunctions::findCampaignName();
        $this->assertEquals(ResultCode::SUCCESS, $resultCode);
        $this->assertEquals(1, count($listCampaignName));
        $this->assertEquals($campaignName->name, $listCampaignName[0]->name);

    }

    public function testDeleteCampaignName()
    {
        $campaignName = factory(CampaignName::class)->create();
        $resultCode = AdminFunctions::deleteCampaignName($campaignName->id);
        $this->assertEquals(ResultCode::SUCCESS, $resultCode);
        $listCampaignName = AdminFunctions::findCampaignName();
        $this->assertEquals(0, count($listCampaignName));

    }


    public function testAddUser()
    {
        $user = new User();
        $user->username = "test";
        $user->password = "test";
        $user->alias_name = "test";
        $user->department = -1;
        $user->role = User::$ROLE_ADMIN;

        $resultCode = AdminFunctions::saveUser($user);

        $users = AdminFunctions::findUsers();
        $this->assertEquals(ResultCode::SUCCESS, $resultCode);
        $this->assertEquals(1, count($users));

        $resultCode = AdminFunctions::saveUser($user);
        $users = AdminFunctions::findUsers();
        $this->assertEquals(ResultCode::FAILED_USER_DUPLICATE_USERNAME, $resultCode);
        $this->assertEquals(1, count($users));

    }

    public function testDeleteUser()
    {
        $user = new User();
        $user->username = "test";
        $user->password = "test";
        $user->alias_name = "test";
        $user->department = -1;
        $user->role = User::$ROLE_ADMIN;
        $user->is_active = true;
        $user->save();
        $resultCode = AdminFunctions::deleteUser($user->id);

        $users = AdminFunctions::findUsers();
        $this->assertEquals(ResultCode::SUCCESS, $resultCode);
        $this->assertEquals(0, count($users));
    }

    public function testAddProduct()
    {
        $product = factory(Product::class)->make();
        $productCategory1 = new \stdClass();
        $productCategory1->size = "39";
        $productCategory1->color = "Đỏ";

        $productCategory2 = new \stdClass();
        $productCategory2->size = "40";
        $productCategory2->color = "Xanh";

        $listDetailProducts = array();
        $detailProduct = new \stdClass();
        $detailProduct->size = $productCategory1->size;
        $detailProduct->color = $productCategory1->color;
        array_push($listDetailProducts, $detailProduct);

        $detailProduct = new \stdClass();
        $detailProduct->size = $productCategory2->size;
        $detailProduct->color = $productCategory2->color;
        array_push($listDetailProducts, $detailProduct);

        $resultCode = AdminFunctions::addProduct($product, $listDetailProducts);
        $listProducts = AdminFunctions::findProducts();
        $this->assertEquals(ResultCode::SUCCESS, $resultCode);
        $this->assertEquals(1, count($listProducts));

        $productRow = AdminFunctions::getProduct($product->code);

        $this->assertEquals(2, ProductCategory::count());
        $this->assertEquals(2, count($productRow->listDetailProducts));
    }
    public function testUpdateProduct(){
        $product = factory(Product::class)->make();
        $productCategory1 = new \stdClass();
        $productCategory1->size = "39";
        $productCategory1->color = "Đỏ";

        $productCategory2 = new \stdClass();
        $productCategory2->size = "40";
        $productCategory2->color = "Xanh";

        $listDetailProducts = array();
        $detailProduct = new \stdClass();
        $detailProduct->size = $productCategory1->size;
        $detailProduct->color = $productCategory1->color;
        array_push($listDetailProducts, $detailProduct);

        $detailProduct = new \stdClass();
        $detailProduct->size = $productCategory2->size;
        $detailProduct->color = $productCategory2->color;
        array_push($listDetailProducts, $detailProduct);

        $resultCode = AdminFunctions::addProduct($product, $listDetailProducts);

        unset($listDetailProducts[1]);
        $resultCode = AdminFunctions::updateProduct($product, $listDetailProducts);
        $this->assertEquals(ResultCode::SUCCESS, $resultCode);
        $productRow = AdminFunctions::getProduct($product->code);
        $this->assertEquals(1, count($productRow->listDetailProducts));
    }

    public function testDeleteProduct(){
        $product = factory(Product::class)->make();
        $productCategory1 = new \stdClass();
        $productCategory1->size = "39";
        $productCategory1->color = "Đỏ";

        $productCategory2 = new \stdClass();
        $productCategory2->size = "40";
        $productCategory2->color = "Xanh";

        $listDetailProducts = array();
        $detailProduct = new \stdClass();
        $detailProduct->size = $productCategory1->size;
        $detailProduct->color = $productCategory1->color;
        array_push($listDetailProducts, $detailProduct);

        $detailProduct = new \stdClass();
        $detailProduct->size = $productCategory2->size;
        $detailProduct->color = $productCategory2->color;
        array_push($listDetailProducts, $detailProduct);

        AdminFunctions::addProduct($product, $listDetailProducts);
        $resultCode = AdminFunctions::deleteProduct($product->code);
        $listProducts = AdminFunctions::findProducts();
        $this->assertEquals(ResultCode::SUCCESS, $resultCode);
        $this->assertEquals(0, count($listProducts));

    }

    public function testSaveDiscount(){
        $discount = factory(Discount::class)->make();
        $resultCode = AdminFunctions::saveDiscount($discount);
        $this->assertEquals(ResultCode::SUCCESS, $resultCode);
        $discountRow = AdminFunctions::getDiscount($discount->id);
        $this->assertEquals($discount->name, $discountRow->name);
        $this->assertEquals($discount->note, $discountRow->note);

        $discount->name = "aaaaaa";
        $resultCode = AdminFunctions::saveDiscount($discount);
        $discountRow = AdminFunctions::getDiscount($discount->id);
        $this->assertEquals($discount->name, $discountRow->name);

    }

    public function fakeAdminUser()
    {
        $user = new User();
        $user->username = "admin";
        $user->password = "admin";
        $user->alias_name = "admin";
        $user->department = -1;
        $user->role = User::$ROLE_ADMIN;

        $resultCode = AdminFunctions::saveUser($user);

    }

}
