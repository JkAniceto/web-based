<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    class order{
        public $dishesArr = array();
        public $priceArr = array();
        public $dishesQuantity = array();
        public $total;
        public $cash;
        public $ordersLinkId;
        public $email;

        function __construct(){ 
            $arguments = func_get_args();
            $numberOfArguments = func_num_args();
            if (method_exists($this, $function = '__construct'.$numberOfArguments)) {
                call_user_func_array(array($this, $function), $arguments);
            }
            
        }

        public function __construct2($ordersLinkId,$email)
        {
            $this -> ordersLinkId = $ordersLinkId;
            $this -> email = $email;

        }

        public function __construct5($dishesQuantity,$dishesArr,$priceArr,$cash,$total)
        {
           $this -> dishesQuantity = $dishesQuantity;
           $this -> dishesArr = $dishesArr;
           $this -> priceArr = $priceArr;
           $this -> cash = $cash;
           $this -> total = $total;
           
        }

        function computeOrder(){
            include('connection.php');
            $sql = mysqli_query($conn,"select dishes_tb.*, order_tb.* from dishes_tb inner join order_tb where dishes_tb.orderType = order_tb.orderType and order_tb.ordersLinkId = '{$this -> ordersLinkId}' ");  
            if (mysqli_num_rows($sql)) {  
                while($rows = mysqli_fetch_assoc($sql)){ 
                    $price = ($rows['price']*$rows['quantity']);  
                    array_push($this-> dishesArr,$rows['dish']);
                    array_push($this-> priceArr,$rows['price']);
                    array_push($this-> dishesQuantity,$rows['quantity']);
                    $this-> total += $price;
                }
            }
        }
        
        function makeReceipt(){
            $dishesArr = $this-> dishesArr;
            $priceArr = $this-> priceArr;
            $dishesQuantity = $this-> dishesQuantity;
            $total = $this-> total;
            $cash = $this -> cash;
            $change = ($cash - $total);
            require_once('TCPDF-main/tcpdf.php'); 
            $obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  
            $obj_pdf->SetCreator(PDF_CREATOR);  
            $obj_pdf->SetTitle("Generate HTML Table Data To PDF From MySQL Database Using TCPDF In PHP");  
            $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
            $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
            $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
            $obj_pdf->SetDefaultMonospacedFont('helvetica');  
            $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
            $obj_pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);  
            $obj_pdf->setPrintHeader(false);  
            $obj_pdf->setPrintFooter(false);  
            $obj_pdf->SetAutoPageBreak(TRUE, 10);  
            $obj_pdf->SetFont('dejavusans', '', 11);  
            $obj_pdf->AddPage(); 
            date_default_timezone_set('Asia/Manila');
            $date = date("j-m-Y  h:i:s A"); 
            $content = '
            <h3>'.$date.'</h3>
            <table  text-center cellspacing="0" cellpadding="3">  
            <tr>
                <th scope="col">Quantity</th>
                <th scope="col">Dish</th>
                <th scope="col">Cost</th>
            </tr>
            ';  
            for($i=0; $i<count($dishesArr); $i++){ 
            $content .= "
            <tr>  
            <td>$dishesQuantity[$i]</td>
            <td>$dishesArr[$i]</td>
            <td>₱$priceArr[$i]</td>
            </tr>
            ";
            }
            $content .= "   
            <br><br>
            <br><br>
            <tr>
                <td></td>
                <td>Cash</td>
                <td>₱$cash</td>
            </tr>
            <tr>
                <td></td>
                <td>Total</td>
                <td>₱$total</td>
            </tr>
            <tr>
                <td></td>
                <td>Change</td>
                <td>₱$change</td>
            </tr>
            <style>
            h3 {text-align: center;}
            table,table td {
                border: 1px solid #cccccc;
            }
    
            td,table{
                text-align: center;
            }
            </style>
            ";
            $obj_pdf->writeHTML($content);  
            ob_end_clean();
            $obj_pdf->Output('file.pdf', 'I');
        }

        function sendReceiptToEmail(){
            $dishesArr = $this-> dishesArr;
            $priceArr = $this-> priceArr;
            $dishesQuantity = $this-> dishesQuantity;
            require_once('TCPDF-main/tcpdf.php'); 
            $obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  
            $obj_pdf->SetCreator(PDF_CREATOR);  
            $obj_pdf->SetTitle("Generate HTML Table Data To PDF From MySQL Database Using TCPDF In PHP");  
            $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
            $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
            $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
            $obj_pdf->SetDefaultMonospacedFont('helvetica');  
            $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
            $obj_pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);  
            $obj_pdf->setPrintHeader(false);  
            $obj_pdf->setPrintFooter(false);  
            $obj_pdf->SetAutoPageBreak(TRUE, 10);  
            $obj_pdf->SetFont('dejavusans', '', 11);  
            $obj_pdf->AddPage(); 
            date_default_timezone_set('Asia/Manila');
            $date = date("j-m-Y  h:i:s A"); 
            $content = '
            <h3>'.$date.'</h3>
            <h3>*******************************************************************</h3>
            <table  text-center cellspacing="0" cellpadding="3">  
            <tr>
                <th scope="col">Quantity</th>
                <th scope="col">Dish</th>
                <th scope="col">Cost</th>
            </tr>
            ';  
            for($i=0; $i<count($dishesArr); $i++){ 
            $content .= "
            <tr>  
            <td>$dishesQuantity[$i]</td>
            <td>$dishesArr[$i]</td>
            <td>₱$priceArr[$i]</td>
            </tr>
            ";
            }
            $content .= "   
            <br><br>
            <br><br>
            <tr>
                <td></td>
                <td>Total</td>
                <td>₱{$this-> total}</td>
            </tr>
            
            <style>
            h3 {text-align: center;}
            table,table td {
                border: 1px solid #cccccc;
            }

            td,table{
                text-align: center;
            }
            </style>
            ";
            $obj_pdf->writeHTML($content);  
            // ob_end_clean();
            $attachment = $obj_pdf->Output('file.pdf', 'S');
            require 'vendor/autoload.php';
            $mail = new PHPMailer(true);
            try {
                $mail->SMTPDebug  = SMTP::DEBUG_OFF;
                //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                     //Enable verbose debug output
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host       = 'smtp.gmail.com';                       //Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                $mail->Username   = 'webBasedOrdering098@gmail.com'; //from //SMTP username
                $mail->Password   = 'cgzyificorxxdlau';                     //SMTP password
                $mail->SMTPSecure =  PHPMailer::ENCRYPTION_SMTPS;           //Enable implicit TLS encryption
                $mail->Port       =  465;                                   //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        
                //Recipients
                $mail->setFrom('webBasedOrdering098@gmail.com', 'webBasedOrdering');
                $mail->addAddress("{$this -> email}");             //sent to
        
                //Content
                $mail->Subject = 'Receipt';
                $mail->Body    = ' ';
                $mail->AddStringAttachment($attachment, 'filename.pdf', 'base64', 'application/pdf');
                $mail->send();
                
                
            }catch (Exception $e) {
                echo "<script>alert('Error: $mail->ErrorInfo');</script>";
            }                        
        }
        
        function approveOrder(){
            require('connection.php');
            $ordersLinkId = $this -> ordersLinkId;
            $updateQuery = "UPDATE orderList_tb SET status=true WHERE ordersLinkId='$ordersLinkId' ";     
            if($conn->query($updateQuery) === FALSE)
                echo "<script>alert('update data unsuccessfully'); window.location.replace('orders.php');</script>";  
            echo "<script>alert('Approve Success'); window.location.replace('orders.php');</script>";
        }
    }

    class orderList{
        function getOrderList(){
            include_once('connection.php');
            $sql = mysqli_query($conn,"select user_tb.*, orderlist_tb.* from user_tb, orderlist_tb where user_tb.userlinkId = orderlist_tb.userlinkId  ORDER BY orderlist_tb.id asc; ");  
            if (mysqli_num_rows($sql)) {
                $arr = array();
                while($rows = mysqli_fetch_assoc($sql)){
                    array_push($arr,$rows);
                }
                return($arr);
            }
        }

        function getApprovedOrderList(){
            include_once('connection.php');
            $sql = mysqli_query($conn,"select user_tb.name, orderlist_tb.* from user_tb, orderlist_tb where user_tb.userlinkId = orderlist_tb.userlinkId and orderlist_tb.status = 1 ORDER BY orderlist_tb.id asc; ");  
            if (mysqli_num_rows($sql)) {
                $arr = array();
                while($rows = mysqli_fetch_assoc($sql)){
                    array_push($arr,$rows);
                }
                return($arr);
            }
        }

        function getOrderListByDates($date1,$date2){
            include_once('connection.php');
            $sql = null;
            // if(empty($date2))
            // $sql = mysqli_query($conn,"select user_tb.name, orderlist_tb.* from user_tb, orderlist_tb where user_tb.userlinkId = orderlist_tb.userlinkId and orderlist_tb.status = 1 and orderlist_tb.date = '$date1' ORDER BY orderlist_tb.id asc; ");  
            // else
            $sql = mysqli_query($conn,"select user_tb.name, orderlist_tb.* from user_tb, orderlist_tb where user_tb.userlinkId = orderlist_tb.userlinkId and orderlist_tb.status = 1 and orderlist_tb.date between '$date1' and '$date2' ORDER BY orderlist_tb.id asc; ");  
            if (mysqli_num_rows($sql)) {
                $arr = array();
                while($rows = mysqli_fetch_assoc($sql)){
                    array_push($arr,$rows);
                }
                return($arr);
            }
        }
    
    }
?>