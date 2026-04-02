const midtransClient = require('midtrans-client');

let snap = new midtransClient.Snap({
    isProduction : false, // Tetap false karena kita di Sandbox
// server key tidak boleh di frontend});

let parameter = {
    "transaction_details": {
        "order_id": "ORDER-101",
        "gross_amount": 10000 // Harga barang
    }
};

snap.createTransaction(parameter)
    .then((transaction)=>{
        let transactionToken = transaction.token;
        console.log('Token:', transactionToken);
    });