<?php
  require_once('config.php');

  $pdo = new PDO("pgsql:host=$host;port=5432;dbname=$db;", $user, $pw);
  $sql = '
    SELECT 
      invoices.id,
      invoice_date,
      total,
      customers.first_name AS first_name,
      customers.last_name AS last_name
    FROM invoices
    INNER JOIN customers
    ON invoices.customer_id = customers.id
  ';

  if (isset($_GET['q'])) {
    $sql = $sql . ' WHERE customers.first_name LIKE :first_name';
  }

  $sql = "$sql ORDER BY first_name, last_name";

  $statement = $pdo->prepare($sql);

  if (isset($_GET['q'])) {
    $boundSearchParam = '%' . $_GET['q'] . '%';
    $statement->bindParam(':first_name', $boundSearchParam);
  }

  $statement->execute();
  $invoices = $statement->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoices</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container">
    <h1>Invoices</h1>

    <form action="invoices.php" method="GET">
      <input
        type="search"
        name="q"
        class="form-control"
        placeholder="Search by first name"
        value="<?php echo isset($_GET['q']) ? $_GET['q'] : '' ?>"
      />
    </form>

    <table class="table table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Date</th>
          <th>Total</th>
          <th>Customer</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($invoices as $invoice) : ?>
          <tr>
            <td>
              <?php echo $invoice->id ?>
            </td>
            <td>
              <?php echo $invoice->invoice_date ?>
            </td>
            <td>
              <?php echo $invoice->total ?>
            </td>
            <td>
              <?php echo $invoice->first_name . " " . $invoice->last_name ?>
            </td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
</body>
</html>