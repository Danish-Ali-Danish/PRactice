<?php
// app/Models/Order.php
namespace App\Models;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',  // ✅ Add this
        'customer_id',
        'total_amount',
        'status'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
