<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'price',
        'description',
        'status',
        'reference_number'
    ];

    public static function getProductPrice($value){
        switch($value){
            case 'product-1':
                // code...
                $price = 1;
                break;
            case 'product-2':
                // code...
                $price = 2;
                
                break;
            case 'product-3':
                // code...
                $price = 3;
            default:
                $price = 0;
                break;
        }
        return $price;
    }

    public static function getProductDescription($value){
        switch($value){
            case 'product-1':
                // code...
                $description = '$1 product';
                break;
            case 'product-2':
                // code...
                $description = '$2 product';
                
                break;
            case 'product-3':
                // code...
                $description = '$3 product';
            default:
                $description = 'Invalid product';
                break;
        }
        return $description;
    }
}
