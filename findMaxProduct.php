<?php
/* *
 * author: Yunlin Xie
 * Midterm1 Assignment: Uploading a file for processing and finding out the largest product.
 * calculateProduct():  input: a string
 *                      output: max product of four adjacent numbers
 * validateContent():  input: a string
 *                     output: FALSE or TRUE
 * test():  input: no input, just for testing the behavior of calculateProduct()
 *          output: no output, just for testing the behavior of calculateProduct()
 * Note1: Please put the midterm1.php in appropriate directory.
 * Note2: Modify the test() function for your own test cases.
 * Note3: All valid input should be able to form a square matrix.
 * */
#######################################################################################################
echo <<<_END
<html>
    <head>
        <title>File Upload</title>
    </head>
    <body>
        <form action = "?" method = "POST" enctype = "multipart/form-data">
            <p><input type="file" name="file"/></p>
            <p><input type="submit" name="upload" value="Upload"></p>
        </form>
_END;

if ($_FILES) {
    // File properties
    $fileName = $_FILES['file']['name'];
    $fileTmpName = $_FILES['file']['tmp_name'];
    $fileSize = $_FILES['file']['size'];
    $fileError = $_FILES['file']['error'];
    // File extension
    $fileExt = explode('.', $fileName);
    $fileExt = strtolower(end($fileExt));
    // Allowed file type
    $allowed = array('txt');

    if (in_array($fileExt, $allowed)) {// Check file type
        if ($fileError === 0) {// Check file error
            if ($fileSize < 1000000) {// Check file size
                if (is_uploaded_file($fileTmpName)) {
                    $fileData = "";
                    $fp = fopen($fileTmpName, 'rb');
                    // Store file in a string without whitespaces and line breaks
                    while ( ($line = fgets($fp)) !== false) {
                        $line = preg_replace("/[ \t]+/", "", preg_replace("/\s*/m", "", $line));
                        $fileData = $fileData.$line;
                    }
                    // Validate input file content
                    if (!validateContent($fileData)) {
                        exit("Invalid file content!<br>Exit program!"); 
                    }
                    // Calculate the max product
                    calculateProduct($fileData);
                    // Test the behavior of calculateProduct()
                    test();
                }
            } else {
                echo "Your file is too big!";
            }
        } else {
            echo "There was an error uploading your file!";
        }
    } else {
        echo "You cannot upload files of this type!";
    }

}
echo "</body></html>";

#######################################################################################################
function calculateProduct($str) {
    $count = 0;
    $len = sqrt(strlen($str));
    $result = 0;
    $nums = array_fill(0, 4, 0);
    $matrixArr = array_fill(0, $len, array_fill(0, $len, 0));
    // Put data into 20x20 array
    for($row=0; $row<$len; $row++) { 
        for($column=0; $column<$len; $column++) {
            $matrixArr[$row][$column] = substr($str, $count, 1);
            $count++;
        }
    }
    // Find max from all rows
    for($row=0; $row<$len; $row++) {
        for($column=3; $column<$len; $column++) {
            $preResult = $result;
            $preNums = $nums;
            $result = $matrixArr[$row][$column-3] * $matrixArr[$row][$column-2] * $matrixArr[$row][$column-1] * $matrixArr[$row][$column];
            $nums = array($matrixArr[$row][$column-3], $matrixArr[$row][$column-2], $matrixArr[$row][$column-1], $matrixArr[$row][$column]);
            if ($result < $preResult) {
                $result = $preResult;
                $nums = $preNums;
            }
        }
    }
    // Find max from all columns 
    for($column=0; $column<$len; $column++) {
        for ($row=3; $row<$len; $row++) {
            $preResult = $result;
            $preNums = $nums;
            $result = $matrixArr[$row-3][$column] * $matrixArr[$row-2][$column] * $matrixArr[$row-1][$column] * $matrixArr[$row][$column];
            $nums = array($matrixArr[$row-3][$column], $matrixArr[$row-2][$column], $matrixArr[$row-1][$column], $matrixArr[$row][$column]);
            if ($result < $preResult) {
                $result = $preResult;
                $nums = $preNums;
            }
        }
    }
    // Find max from all diagonals 
    for($row=0; $row<$len; $row++) {
        for($column=0; $column<$len; $column++) {
            if (($row+3)<$len && ($column+3)<$len) {
                $preResult = $result;
                $preNums = $nums;
                $result = $matrixArr[$row][$column] * $matrixArr[$row+1][$column+1] * $matrixArr[$row+2][$column+2] * $matrixArr[$row+3][$column+3];
                $nums = array($matrixArr[$row][$column], $matrixArr[$row+1][$column+1], $matrixArr[$row+2][$column+2], $matrixArr[$row+3][$column+3]);
                if ($result < $preResult) {
                    $result = $preResult;
                    $nums = $preNums;
                }
            }
            if (($row+3)<$len && ($column-3)>=0) {
                $preResult = $result;
                $preNums = $nums;
                $result = $matrixArr[$row][$column] * $matrixArr[$row+1][$column-1] * $matrixArr[$row+2][$column-2] * $matrixArr[$row+3][$column-3];
                $nums = array($matrixArr[$row][$column], $matrixArr[$row+1][$column-1], $matrixArr[$row+2][$column-2], $matrixArr[$row+3][$column-3]);
                if ($result < $preResult) {
                    $result = $preResult;
                    $nums = $preNums;
                }
            }
        }
    }
    echo "The greatest product is ".$result;
    echo "<br>";
    echo "It is resulted from numbers: ".$nums[0].", ".$nums[1].", ".$nums[2].", ".$nums[3];
    echo "<br>";
    return $result;
}// end of calculateProduct()

#######################################################################################################
function validateContent($content) {
    if (ctype_digit($content)) {// Check if input only contains digits
        if (strlen($content) == 400) {// Validate the length of input
            return TRUE;
        } else {
            echo "File should contain exactly 400 digits!";
            echo "<br>";
        }
    } else {
        echo "File should contain digits only!";
        echo "<br>";
    }
    return FALSE;
} // end of validateContent()

#######################################################################################################
function test() {
    echo "<br>The following is for testing the function calculateProduct(), the size of inputting string should be greater than 16.<br>";

    // Test 1: 4x4 should return 0
    echo "<br>Test Case 1:<br>";
    echo "0 0 0 0<br>";
    echo "0 0 0 0<br>";
    echo "0 0 0 0<br>";
    echo "0 0 0 0<br>";
    $r1 = calculateProduct("0000"."0000"."0000"."0000");
    if ($r1 == 0) {
        echo "From test(): The result is ".$r1.", and it was test to be true!<br>";
    } else {
        echo "From test(): The result is ".$r1.", but it was test to be false!<br>";
    }

    // Test 2: 4x4 should return 16
    echo "<br>Test Case 2:<br>";
    echo "2 2 2 2<br>";
    echo "2 2 2 2<br>";
    echo "2 2 2 2<br>";
    echo "2 2 2 2<br>";
    $r2 = calculateProduct("2222"."2222"."2222"."2222");
    if ($r2 == 16) {
        echo "From test(): The result is ".$r2.", and it was test to be true!<br>";
    } else {
        echo "From test(): The result is ".$r2.", but it was test to be false!<br>";
    }

    // Test 3: 5x5 should return 6561
    echo "<br>Test Case 3:<br>";
    echo "9 9 9 9 9<br>";
    echo "8 8 8 8 8<br>";
    echo "7 7 7 7 7<br>";
    echo "6 6 6 6 6<br>";
    echo "0 0 0 0 0<br>";
    $r3 = calculateProduct("99999"."88888"."77777"."66666"."00000");
    if ($r3 == 6561) {
        echo "From test(): The result is ".$r3.", and it was test to be true!<br>";
    } else {
        echo "From test(): The result is ".$r3.", but it was test to be false!<br>";
    }

    // Test 4: 6x6 should return 3024
    echo "<br>Test Case 4:<br>";
    echo "0 0 0 0 0 9<br>";
    echo "0 0 0 0 0 8<br>";
    echo "0 0 0 0 0 7<br>";
    echo "0 0 0 0 0 6<br>";
    echo "0 0 0 0 0 5<br>";
    echo "0 0 0 0 0 4<br>";
    $r4 = calculateProduct("000009"."000008"."000007"."000006"."000005"."000004");
    if ($r4 == 3024) {
        echo "From test(): The result is ".$r4.", and it was test to be true!<br>";
    } else {
        echo "From test(): The result is ".$r4.", but it was test to be false!<br>";
    }
}// end of test()

?>


