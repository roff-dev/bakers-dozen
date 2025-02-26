<div class="form-list-container">
    <form class="forms-container" name="contactForm" id="contact-form" onsubmit="return validateForm()" method="POST">
        <div class="form-top">
            <input id="firstname" class="form form-control" type="text" name="firstname" placeholder="First Name*" value="">
        </div>
        <div class="form-top">
            <input id="lastname"  class="form form-control" type="text" name="lastname" placeholder="Last Name*" value="">
        </div>
        <div class="form-bottom">
            <input class="form form-control" id="telephone" type="text" name="telephone" placeholder="Phone Number*" value="">
        </div>
        <div class="form-bottom">
            <input class="form form-control" id="email" type="email" name="email" placeholder="Email Address*" value="">
        </div>
        <div class="form-bottom">
            <input id="subject" class="form form-control" type="text" name="subject" placeholder="Subject*" value="">
        </div>
        <div class="form-bottom">
            <textarea id="message" class="form form-end form-control"  name="message" placeholder="Message*" value=""></textarea>
        </div>
        <div class="form-btn-container">
            <input class="btn-submit" id="submitBtn" type="button" value="Submit">
        </div>
    </form>
</div>