jQuery(document).ready(function () {
    jQuery('#w0').yiiActiveForm([{
        "id": "user-username",
        "name": "username",
        "container": ".field-user-username",
        "input": "#user-username",
        "validate": function (attribute, value, messages, deferred, $form) {
            yii.validation.required(value, messages, {"message": "Username cannot be blank."});
            yii.validation.string(value, messages, {
                "message": "Username must be a string.",
                "max": 255,
                "tooLong": "Username should contain at most 255 characters.",
                "skipOnEmpty": 1
            });
        }
    }, {
        "id": "user-email",
        "name": "email",
        "container": ".field-user-email",
        "input": "#user-email",
        "validate": function (attribute, value, messages, deferred, $form) {
            yii.validation.required(value, messages, {"message": "Email cannot be blank."});
            yii.validation.string(value, messages, {
                "message": "Email must be a string.",
                "max": 255,
                "tooLong": "Email should contain at most 255 characters.",
                "skipOnEmpty": 1
            });
            yii.validation.email(value, messages, {
                "pattern": /^[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/,
                "fullPattern": /^[^@]*<[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/,
                "allowName": false,
                "message": "Email is not a valid email address.",
                "enableIDN": false,
                "skipOnEmpty": 1
            });
        }
    }, {
        "id": "user-birth_date",
        "name": "birth_date",
        "container": ".field-user-birth_date",
        "input": "#user-birth_date",
        "validate": function (attribute, value, messages, deferred, $form) {
            yii.validation.required(value, messages, {"message": "Birth Date cannot be blank."});
        }
    }, {
        "id": "user-password",
        "name": "password",
        "container": ".field-user-password",
        "input": "#user-password",
        "validate": function (attribute, value, messages, deferred, $form) {
            yii.validation.required(value, messages, {"message": "Password cannot be blank."});
        }
    }, {
        "id": "user-confirm_password",
        "name": "confirm_password",
        "container": ".field-user-confirm_password",
        "input": "#user-confirm_password",
        "validate": function (attribute, value, messages, deferred, $form) {
            yii.validation.required(value, messages, {"message": "Confirm Password cannot be blank."});
            yii.validation.compare(value, messages, {
                "operator": "==",
                "type": "string",
                "compareValue": "jeel",
                "skipOnEmpty": 1,
                "message": "Confirm Password must be equal to \"jeel\"."
            });
        }
    }], []);
});