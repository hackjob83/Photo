function validateForm() {
    var isValid = true;
    var invalidArray = new Array();    
    var fieldReg = /^[a-zA-Z\ \'\-.]*$/;


    if ($.trim($('#fname').val()) === '' || $.trim($('#fname').val()).length < 2 || fieldReg.test($.trim($('#fname').val())) === false) {
        isValid = false;
        invalidArray.push('A first name is required');
        $('#fname').css({
            'background-color': 'lightcoral'
        });
    } else {
        $('#fname').css({
            'background-color': 'white'
        });
    }
    if ($.trim($('#lname').val()) === '' || $.trim($('#lname').val()).length < 2 || fieldReg.test($.trim($('#lname').val())) === false) {
        isValid = false;
        invalidArray.push('A last name is required');
        $('#lname').css({
            'background-color': 'lightcoral'
        });
    } else {
        $('#lname').css({
            'background-color': 'white'
        });
    }
    
    
    if ($('#photo').val() === '') {
        isValid = false;
        invalidArray.push('You must upload your photo');
        $('#upload_div').css({
            'background-color': 'lightcoral'
        });
    } else {
        $('#upload_div').css({
            'background-color': 'white'
        });
    }


    
    if (!isValid) {
        var alertString = 'The following errors were found:\n\n';
        for (var item in invalidArray) {
            alertString += '-- ' + invalidArray[item] + '\n';
        }

        //alert(alertString);
    }

    return isValid;
}
