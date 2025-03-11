<div class="form-paragraph-container">
    <h3>Order Now</h3>
    <p>Via this form.</p>
    <div class="alert alert-danger-hidden" id="alert">

    </div>
</div>

<div class="form-list-container">
    <form class="forms-container" name="orderForm" id="order-form" action="../../src/php/orderform.php" onsubmit="return validateForm()" method="POST">
        <div class="form-top">
            <input id="name" class="form form-control" type="text" name="name" placeholder="Name*" value="" required>
        </div>
        <div class="form-bottom">
            <input class="form form-control" id="address" type="text" name="address" placeholder="Address*" value="" required>
        </div>
        <div class="form-contact">
            <input class="form form-control" id="email" type="email" name="email" placeholder="Email Address*" value="" required>
        </div>
        <div class="form-contact">
            <input class="form form-control" id="telephone" type="text" name="telephone" placeholder="Phone Number*" value="" required>
        </div>
        <div class="form-bottom">
            <input id="subject" class="form form-control" type="text" name="subject" placeholder="Subject*" value="">
        </div>
        <div class="form-bottom">
            <textarea id="message" class="form form-end form-control"  name="message" placeholder="Message*" value=""></textarea>
        </div>
        <div class="form-btn-container">
            <button class="btn-submit" id="submitBtn" type="button" value="Submit">Submit</button>
        </div>
    </form>
</div>