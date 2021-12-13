<?php
class Database
{
  public $sql;
  private $dsn = "mysql:host=localhost;dbname=crud_oop";
  private $username = "root";
  private $password = "";
  private $conn = "";
  public function __construct()
  {
    try {
      $this->conn = new PDO($this->dsn, $this->username, $this->password);
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
    }
  }
  public function crud($operation, $table_name, $columns, $values)
  {
    // $OPERATION=> INSERT UPDATE DELETE SELECT
    //SELECT SELECTS ALL COLUMUNS
    //$columns will be an array the first index will be id
    //$values will be an array the first index will be id
    //WHEN USING THE INSERT DON"T INSERT THE ID
    //DONT USE ID IN INSERT
    //PUT THE ID IN THE FIRST INDEX
    $query = "";
    //delete all the row items
    $length = count($columns) - 1;
    $i = 0;
    if ($operation === "DELETE") {

      $query = "DELETE FROM {$table_name} WHERE  {$columns[0]} = '{$values[0]}'";
    }
    if ($operation === "UPDATE") {
      $query = "UPDATE " . $table_name . " SET";
      $query_values = "";

      for ($i = 0; $i <= $length; $i++) {
        if ($i === 0) continue;
        if ($i === $length) {
          if (is_numeric($values[$i])) {
            $query_values .= " {$columns[$i]}={$values[$i]} WHERE";
          } else {
            $query_values .= " {$columns[$i]}='{$values[$i]}' WHERE";
          }
        } else {
          if (is_numeric($values[$i])) {
            $query_values .= " {$columns[$i]}={$values[$i]},";
          } else {
            $query_values .= " {$columns[$i]}='{$values[$i]}',";
          }
        }
      }
      $query .= " " . $query_values;
      $query .= " {$columns[0]}={$values[0]}";
    }
    if ($operation === "INSERT") {
      $query = "INSERT INTO " . $table_name;
      $query_values = "VALUES ";

      for ($i = 0; $i <= $length; $i++) {
        if ($i === 0) {
          $query .= "({$columns[$i]},";
          //remove '' when the value is number
          if (is_numeric($values[$i])) {
            $query_values .= "({$values[$i]} ,";
          } else {
            $query_values .= "('{$values[$i]}' ,";
          }
        } elseif ($i === $length) {
          $query .= "{$columns[$i]})";
          //remove '' when the value is number
          if (is_numeric($values[$i])) {
            $query_values .= "{$values[$i]})";
          } else {
            $query_values .= "'{$values[$i]}')";
          }
        } else {
          $query .= "{$columns[$i]},";
          if (is_numeric($values[$i])) {
            $query_values .= "{$values[$i]},";
          } else {
            $query_values .= "'{$values[$i]}',";
          }
        }
      }
      $query .= " " . $query_values;
    }
    $statement = $this->conn->prepare($query);
    $statement->execute();
  }
  function getAllFrom($field, $table, $where = NULL, $and = NULL, $orderfield, $ordering = "DESC")
  {

    $getAll = $this->conn->prepare("SELECT $field FROM $table $where $and ORDER BY $orderfield $ordering");

    $getAll->execute();

    $all = $getAll->fetchAll();

    return $all;
  }
}

$showTable = new Database();
$select = $showTable->getAllFrom("*", "coffee", null, null, 'coffee_id', null);

if (isset($_GET['do'])) {
  if ($_GET['do'] == 'delete') {
    $id = $_GET['id'];
    $showTable->crud("DELETE", 'coffee', ["coffee_id"], [$id]);
    header("location:index.php");
  }
  if ($_GET['do'] == 'edit') {
    $id = $_GET['id'];
    $edit = $showTable->getAllFrom("*", "coffee", "WHERE coffee_id = $id", null, 'coffee_id', null);
    // crud($operation, $table_name, $columns, $values)
    if (isset($_POST['submit'])) {
      $name = $_POST['name'];
      $price = $_POST['price'];
      $type = $_POST['type'];
      $country = $_POST['country'];
      $showTable->crud("UPDATE", 'coffee', ['coffee_id', 'coffee_name', 'coffee_price', 'coffee_type', 'coffee_country'], [$id, "$name", $price, $type, "$country"]);
      header("location:index.php");
    }
  }
  if ($_GET['do'] == 'add') {
    // crud($operation, $table_name, $columns, $values)
    if (isset($_POST['submit'])) {
      $name = $_POST['name'];
      $price = $_POST['price'];
      $type = $_POST['type'];
      $country = $_POST['country'];
      $showTable->crud("INSERT", 'coffee', ['coffee_name', 'coffee_price', 'coffee_type', 'coffee_country'], ["$name", $price, $type, "$country"]);
      header("location:index.php");
    }
  }
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <title>Coffee</title>
</head>

<body>
  <?php if (isset($_GET['do'])) {
    //if ($_GET['do'] == 'edit' || $_GET['do'] == 'add') { 
  ?>
    <form method="POST" class="min-h-screen bg-gray-100 py-6 flex flex-col justify-center sm:py-12">
      <div class="relative py-3 sm:max-w-xl sm:mx-auto">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-300 to-blue-600 shadow-lg transform -skew-y-6 sm:skew-y-0 sm:-rotate-6 sm:rounded-3xl">
        </div>
        <div class="relative px-4 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-20">
          <div class="max-w-md mx-auto">
            <div>
              <h1 class="text-2xl font-semibold"><?php echo $_GET['do'] == 'edit' ? "Edit" : "Add"; ?> Coffee</h1>
            </div>
            <div class="divide-y divide-gray-200">
              <div class="py-8 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
                <div class="relative">
                  <input name="name" value="<?php echo $_GET['do'] == 'edit' ? $edit[0]['coffee_name'] : ""; ?>" id="name" type="text" class="peer placeholder-transparent h-10 w-full border-b-2 border-gray-300 text-gray-900 focus:outline-none focus:borer-rose-600" placeholder="Coffee Name" />
                  <label for="name" class="absolute left-0 -top-3.5 text-gray-600 text-sm peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-440 peer-placeholder-shown:top-2 transition-all peer-focus:-top-3.5 peer-focus:text-gray-600 peer-focus:text-sm">Coffee Name</label>
                </div>
                <div class="relative">
                  <input name="price" value="<?php echo $_GET['do'] == 'edit' ? $edit[0]['coffee_price'] : ""; ?>" id="price" type="number" class="peer placeholder-transparent h-10 w-full border-b-2 border-gray-300 text-gray-900 focus:outline-none focus:borer-rose-600" placeholder="Coffee Price" />
                  <label for="price" class="absolute left-0 -top-3.5 text-gray-600 text-sm peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-440 peer-placeholder-shown:top-2 transition-all peer-focus:-top-3.5 peer-focus:text-gray-600 peer-focus:text-sm">Coffee Price</label>
                </div>
                <div class="relative">
                  <input name="type" value="<?php echo $_GET['do'] == 'edit' ? $edit[0]['coffee_type'] : ""; ?>" id="type" type="text" class="peer placeholder-transparent h-10 w-full border-b-2 border-gray-300 text-gray-900 focus:outline-none focus:borer-rose-600" placeholder="Coffee Type" />
                  <label for="type" class="absolute left-0 -top-3.5 text-gray-600 text-sm peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-440 peer-placeholder-shown:top-2 transition-all peer-focus:-top-3.5 peer-focus:text-gray-600 peer-focus:text-sm">Coffee Type</label>
                </div>
                <div class="relative">
                  <input name="country" value="<?php echo $_GET['do'] == 'edit' ? $edit[0]['coffee_country'] : ""; ?>" id="country" type="text" class="peer placeholder-transparent h-10 w-full border-b-2 border-gray-300 text-gray-900 focus:outline-none focus:borer-rose-600" placeholder="Coffee Country" />
                  <label for="country" class="absolute left-0 -top-3.5 text-gray-600 text-sm peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-440 peer-placeholder-shown:top-2 transition-all peer-focus:-top-3.5 peer-focus:text-gray-600 peer-focus:text-sm">Coffee Country</label>
                </div>
                <div class="relative">
                  <button type="submit" name="submit" class="bg-blue-500 text-white rounded-md px-2 py-1"><?php echo $_GET['do'] == 'edit' ? "Edit" : "Add"; ?></button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  <?php //}
  } ?>
  <?php if (!isset($_GET['do'])) { ?>
    <div class="bg-white p-8 rounded-md w-full">
      <div class=" flex items-center justify-between pb-6">
        <div>
          <h2 class="text-gray-600 font-semibold">Coffee Shop</h2>
          <span class="text-xs">All products item</span>
        </div>
        <div class="flex items-center justify-between">
          <div class="flex bg-gray-50 items-center p-2 rounded-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
            <input class="bg-gray-50 outline-none ml-1 block " type="text" name="" id="" placeholder="search...">
          </div>
          <div class="lg:ml-40 ml-10 space-x-8">
            <a href="?do=add"><button class="bg-indigo-600 px-4 py-2 rounded-md text-white font-semibold tracking-wide cursor-pointer">Create</button></a>
          </div>
        </div>
      </div>
      <div>
        <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
          <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
            <table class="min-w-full leading-normal">
              <thead>
                <tr>
                  <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Name
                  </th>
                  <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Price
                  </th>
                  <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Type
                  </th>
                  <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Country
                  </th>
                  <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                  </th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($select as $key => $value) {
                  // echo "<pre>";
                  // print_r($value);
                  // echo "</pre>";
                ?>
                  <tr>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                      <div class="flex items-center">
                        <div class="ml-3">
                          <p class="text-gray-900 whitespace-no-wrap">
                            <?php echo $value['coffee_name'] ?>
                          </p>
                        </div>
                      </div>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                      <p class="text-gray-900 whitespace-no-wrap"><?php echo "$" . $value['coffee_price'] ?></p>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                      <p class="text-gray-900 whitespace-no-wrap">
                        <?php echo $value['coffee_type'] ?>
                      </p>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                      <p class="text-gray-900 whitespace-no-wrap">
                        <?php echo $value['coffee_country'] ?>
                      </p>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                      <span class="relative cursor-pointer"><a href="?do=edit&id=<?php echo $value['coffee_id'] ?>"><i class="far fa-edit"></i></a></span>
                      <span class="relative cursor-pointer"><a href="?do=delete&id=<?php echo $value['coffee_id'] ?>"><i class="far fa-trash-alt"></i></a></span>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
            <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
              <span class="text-xs xs:text-sm text-gray-900">
                Showing 1 to 4 of 50 Entries
              </span>
              <div class="inline-flex mt-2 xs:mt-0">
                <button class="text-sm text-indigo-50 transition duration-150 hover:bg-indigo-500 bg-indigo-600 font-semibold py-2 px-4 rounded-l">
                  Prev
                </button>
                &nbsp; &nbsp;
                <button class="text-sm text-indigo-50 transition duration-150 hover:bg-indigo-500 bg-indigo-600 font-semibold py-2 px-4 rounded-r">
                  Next
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>
</body>

</html>