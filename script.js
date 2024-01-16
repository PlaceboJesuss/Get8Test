phoneMaskedInput = IMask(
    document.getElementById('phone'),
    {
        mask: '+{7}(000)000-00-00',
        lazy: false,
    }
);
function clickSubmit(e) {
    const $form = $(e.target);
    const $inputs = $form.find(".input-container input");
    const $messageContainer = $form.find(".message-container");

    $form.find("input[type='submit']").prop("disabled", true);
    $inputs.removeClass("input-incorect");
    $messageContainer.hide();
    $messageContainer.removeClass("message-error");
    $messageContainer.removeClass("message-success");

    let data = { form_id: $form.id };
    for (const input of $inputs) {
        if (input.id === 'phone') {
            data[input.name] = phoneMaskedInput.unmaskedValue;
        } else {
            data[input.name] = input.value;
        }
    }
    $.post("form.php", data, "json").then((e) => { requestHandler(e, $form) });
    return false;
}

function requestHandler(result, $form) {
    $form.find("input[type='submit']").prop("disabled", false);
    if (result["result"] == "error") {
        if (result["error_fields"]) {

            for (const errField of result["error_fields"]) {
                $form.find(`[name='${errField}']`).addClass("input-incorect");
            }
        }
        if (result["message"]) {
            const $messageContainer = $form.find(".message-container");
            $messageContainer.addClass("message-error");
            $messageContainer.text(result["message"]);
            $messageContainer.show();
        }
    } else {
        if (result["message"]) {
            const $messageContainer = $form.find(".message-container");
            $messageContainer.addClass("message-success");
            $messageContainer.text(result["message"]);
            $messageContainer.show();
        }
    }
}
$("#test-form").on("submit", clickSubmit);