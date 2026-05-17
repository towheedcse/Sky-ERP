<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="./style.css" />
    <title>LC report</title>
    <script src="./script.js" defer></script>
  </head>
  <body>
    <div class="navbar bg-base-100 shadow-sm">
      <div class="navbar-start">
        <div class="dropdown">
          <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h7"
              />
            </svg>
          </div>
          
        </div>
      </div>
      <div class="navbar-center">
        <a class="btn btn-ghost text-xl">LC report</a>
      </div>
      <div class="navbar-end">
        <button class="btn btn-ghost btn-circle">
          <div class="indicator" onclick="toggleTheme()">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="w-5 h-5"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"
              />
            </svg>
          </div>
        </button>
      </div>
    </div>
    <div class="px-4 py-2 md:px-12 md:py-4">
      <h1 class="text-3xl font-bold mb-3"></h1>
      <form
        class="px-1 py-2 md:px-7 md:py-8 shadow-lg rounded-xl flex items-end flex-wrap gap-3 ml-2"
      >
        <label class="form-control w-full max-w-xs md:max-w-sm">
          <div class="label">
            <span class="label-text font-bold">From</span>
          </div>
          <input
            type="date"
            class="input input-ghost input-bordered hover:bg-base-200 w-full max-w-xs md:max-w-sm"
          />
        </label>
        <label class="form-control w-full max-w-xs md:max-w-sm">
          <div class="label">
            <span class="label-text font-bold">To</span>
          </div>
          <input
            type="date"
            class="input input-ghost input-bordered hover:bg-base-200 w-full max-w-xs md:max-w-sm"
          />
        </label>
        <label class="form-control flex-1 relative">
          <div class="label">
            <span class="label-text font-bold">LC Number</span>
          </div>
          <input
            type="text"
            placeholder="Type here"
            class="input input-ghost input-bordered hover:bg-base-200 w-full"
          />
          <div class="input-suggestions-dd active">
            <!-- add all the batch list -->
            <div class="input-suggestion">have to find</div>
            <div class="input-suggestion">taddakdd</div>
            <div class="input-suggestion">hello, world</div>
          </div>
        </label>
        <button class="btn btn-primary rounded-lg">Submit</button>
      </form>
      <div class="py-3 md:py-8 px-2 md:px-5 mt-3 rounded-lg shadow-lg ml-2">
        <div
          class="flex items-center justify-center sm:justify-between gap-3 flex-wrap mb-3"
        >
          <h2 class="text-xl font-bold mb-3"></h2>
          <div class="join">
            <button
              class="join-item btn hover:bg-primary hover:text-primary-content"
            >
              «
            </button>
            <div class="dropdown join-item">
              <div tabindex="0" role="button" class="btn rounded-none">
                page 1
              </div>
              <ul
                tabindex="0"
                class="dropdown-content z-[1] menu shadow bg-base-100 p-0 w-full"
              >
                <li><a class="text-center">page 2</a></li>
                <li><a class="text-center">page 3</a></li>
                <li><a class="text-center">page 4</a></li>
                <li><a class="text-center">page 5</a></li>
              </ul>
            </div>
            <button
              class="join-item btn hover:bg-primary hover:text-primary-content"
            >
              »
            </button>
          </div>
        </div>
        <div class="text-center font-medium">
           <h2 class="text-xl font-bold mb-3">LC Transaction List</h2>
          <div class="text-lg">Heritage Polymer & Lami Tubes Ltd.</div>
          <div>Gulshan-1, Dhaka, bangladesh</div>
        </div>
        <div class="overflow-x-auto mt-3">
          <table class="table table-zebra">
            <!-- head -->
            <thead>
              <tr>
                <th>Date</th>
                <th>Voucher No.</th>
                <th>Voucher Type</th>
                <th>Account Name</th>
                <th>LC Number</th>
                <th>Debit (BDT)</th>
                <th>Credit (BDT)</th>
                <th class="text-center">Created By</th>
              </tr>
            </thead>
            <tbody>
<?php

// Database credentials
$hostname = 'localhost';
$username = 'root';
$password = ''; // No password
$database = 'resindb2022'; // Replace with your actual database name

// Create a connection
$connection = mysqli_connect($hostname, $username, $password, $database);

// Check the connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// SQL query
$sql = "SELECT * 
        FROM sub_acc_head 
        INNER JOIN account_journal ON sub_acc_head.sub_id = account_journal.sub_id 
        WHERE account_journal.description = ''";

// Execute the query
$result = mysqli_query($connection, $sql);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}

// Fetch and display the results
while ($row = mysqli_fetch_assoc($result)) {
    ?>

<tr>
                <th><?php echo  $row['created_date']; ?></th>
                <td><?php echo  $row['voucher_no']; ?></td>
                <td>
                  
                  <div><?php echo  $row['head_type']; ?></div>
                </td>
                <td>
                  <div><?php echo  $row['sub_head_name']; ?></div>
                  
                </td>
                <td>
                  <div><?php echo  $row['description']; ?></div>
                </td>
                <td>
                  <div><?php echo  $row['dr']; ?></div>
                  
                </td>
                <td>
                  <div><?php echo  $row['cr']; ?></div>
                  
                </td>
                 <td>
                  <div><?php echo  $row['created_by']; ?></div>
                  
                </td>
                <td>
                  <div class="flex items-center justify-center gap-1">
                    <!-- option buttons -->
                    <button class="btn btn-ghost">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-5 h-5"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"
                        />
                      </svg>
                    </button>
                    <button class="btn btn-ghost">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-7 h-7"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M6 18 18 6M6 6l12 12"
                        />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>

<?
}

// Close the connection
mysqli_close($connection);

?>


              <!-- row exmp -->
              
              <tr>
                <td
                  colspan="5"
                  class="text-right border-r border-primary capitalize"
                >
                  total
                </td>
                <td>4958.00 Tk</td>
                <td>4958.00 Tk</td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </body>
</html>

