$('.ui.checkbox')
    .checkbox()
    .first().checkbox({
    onChecked: function () {
        document.querySelector(".ui.disabled.button").className = "ui submit button";
    },
    onUnchecked: function () {
        document.querySelector(".ui.button").className = "ui disabled submit button";
    }
});
$('.ui.dropdown')
    .dropdown()
;
$('.menu .item')
    .tab()
;
