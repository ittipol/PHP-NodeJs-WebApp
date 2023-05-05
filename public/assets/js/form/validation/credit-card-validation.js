$(function() {

    // let owner = $('#owner');
    let cardNumber = $('#card_number');
    let cvc = $("#cvc");
    let expire = $("#card_expire");

    let currentCard = null;
    let creditCard = $("#credit_card_image");

    // Use the payform library to format and validate
    // the payment fields.

    cardNumber.payment('formatCardNumber');
    cvc.payment('formatCardCVC');
    expire.payment('formatCardExpiry');

    cardNumber.keyup(function() {
        
        if($.payment.cardType(cardNumber.val()) == currentCard) {
            return false;
        }

        switch($.payment.cardType(cardNumber.val())) {

            case 'visa':
                creditCard.attr('src','/images/credit_card/visa.png');
                currentCard = 'visa';
            break;

            case 'amex':
                creditCard.attr('src','/images/credit_card/americanexpress.png');
                currentCard = 'amex';
            break;

            case 'mastercard':
                creditCard.attr('src','/images/credit_card/mastercard.png');
                currentCard = 'mastercard';
            break;

            default:
                creditCard.attr('src','/images/credit_card/card.png');

        }

    });

});
