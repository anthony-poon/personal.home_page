import anime from "animejs"

$(document).ready(() => {
    $("#js-rhs-1, #js-lhs-name").each(function() {
        // Wrap every letter with span
        let text = $(this).text().trim();
       $(this).html(text.replace(/(.)/g, "<span class='letter'>$&</span>"))
    });
    anime.timeline().add({
        targets: '#js-lhs-name .letter',
        scale: [0.3,1],
        opacity: [0,1],
        translateZ: 0,
        easing: "easeOutExpo",
        duration: 600,
        delay: function(el, i) {
            return 70 * (i+1)
        }
    }).add({
        targets: "#js-lhs .line",
        scaleX: [0,1],
        opacity: [0.5,1],
        easing: "easeOutExpo",
        duration: 700,
        delay: function(el, i, l) {
            return 80 * (l - i);
        }
    }).add({
        targets: "#js-rhs-1 .letter",
        opacity: [0, 1],
        easing: "easeOutExpo",
        duration: 1200,
        delay: function(el, i) {
            return 500 + 30 * i;
        },
        endDelay: function(el, i, l) {
            return 50;
        }
    }).add({
        targets: "#js-rhs-2",
        opacity: [0, 1],
        easing: "easeOutExpo",
        duration: 1200,
        endDelay: function(el, i, l) {
            return 0;
        }
    }).add({
        targets: "#js-rhs-3",
        opacity: [0, 1],
        easing: "easeOutExpo",
        duration: 1200,
        endDelay: function(el, i, l) {
            return 50;
        }
    }).add({
        targets: "#js-rhs-4",
        opacity: [0, 1],
        easing: "easeOutExpo",
        duration: 1200,
        endDelay: function(el, i, l) {
            return 50;
        }
    }).add({
        targets: "#js-rhs-5",
        opacity: [0, 1],
        easing: "easeOutExpo",
        duration: 1200,
        endDelay: function(el, i, l) {
            return 50;
        }
    }).add({
        targets: "#js-rhs-6",
        opacity: [0, 1],
        easing: "easeOutExpo",
        duration: 1200,
        endDelay: function(el, i, l) {
            return 0;
        }
    }).add({
        targets: "#js-rhs-7",
        opacity: [0, 1],
        easing: "easeOutExpo",
        duration: 1200,
        endDelay: function(el, i, l) {
            return 0;
        }
    }).add({
        targets: "#js-rhs-8",
        opacity: [0, 1],
        easing: "easeOutExpo",
        duration: 1200,
        endDelay: function(el, i, l) {
            return 50;
        }
    })
});