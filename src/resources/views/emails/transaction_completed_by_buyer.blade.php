<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>取引完了メール</title>
  </head>

  <body>
    <p>{{ $transaction->purchase->seller->name ?? '出品者' }} 様</p>

    <p>購入者が取引を完了しました。</p>

    <ul>
      <li>商品：{{ $transaction->purchase->item->item_name ?? '-' }}</li>
      <li>取引ID：{{ $transaction->id }}</li>
    </ul>

    <p>取引画面で内容をご確認ください。</p>
  </body>
</html>