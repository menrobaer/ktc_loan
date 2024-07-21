var tooltipTriggerList = [].slice.call(
  document.querySelectorAll('[data-toggle="tooltip"]')
);
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl);
});

$(document).on("click", "form input[type='number']", function () {
  $(this).select();
});

$(document).on("change", "form input[type='number']", function () {
  var value = $(this).val();
  if (value == "" || value.length == 0) {
    $(this).val(0);
  }
});

function toNumber(value) {
  return value == "" || isNaN(value) ? 0 : parseFloat(value);
}

yii.confirm = function (message, ok, cancel) {
  var url = $(this).data("url");
  if ($(this).hasClass("button-action-swal")) {
    var title = $(this).prop("title") ? $(this).prop("title") : "Are you sure?";
    Swal.fire({
      title: title,
      text: message,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes please.",
      cancelButtonText: "Cancel",
    }).then((result) => {
      if (result.isConfirmed) {
        $.post(url);
      }
    });
  }
  if ($(this).hasClass("button-delete")) {
    Swal.fire({
      title: "Are you sure?",
      text: message,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, Delete it.",
      cancelButtonText: "Cancel",
    }).then((result) => {
      if (result.isConfirmed) {
        $.post(url);
      }
    });
  }
  if ($(this).hasClass("sign-out-user")) {
    Swal.fire({
      title: "Warning!",
      text: "Are you sure you want to logout?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, Logout now!",
    }).then((result) => {
      if (result.isConfirmed) {
        $.post(url);
      }
    });
  }
};

// Function to convert numbers to words
function numberToWords(num) {
  const a = [
    "",
    "one",
    "two",
    "three",
    "four",
    "five",
    "six",
    "seven",
    "eight",
    "nine",
  ];
  const b = [
    "",
    "",
    "twenty",
    "thirty",
    "forty",
    "fifty",
    "sixty",
    "seventy",
    "eighty",
    "ninety",
  ];
  const c = [
    "",
    "one",
    "two",
    "three",
    "four",
    "five",
    "six",
    "seven",
    "eight",
    "nine",
    "ten",
    "eleven",
    "twelve",
    "thirteen",
    "fourteen",
    "fifteen",
    "sixteen",
    "seventeen",
    "eighteen",
    "nineteen",
  ];

  const convertLessThanThousand = (num) => {
    let str = "";

    if (num >= 100) {
      str += a[Math.floor(num / 100)] + " hundred ";
      num %= 100;
    }

    if (num >= 20) {
      str += b[Math.floor(num / 10)] + "-";
      num %= 10;
    } else if (num >= 10) {
      str += c[num];
      num = 0;
    }

    if (num > 0) {
      str += a[num];
    }

    return str.trim();
  };

  if (num === 0) return "zero";

  let words = "";

  if (num >= 1000000000) {
    words +=
      convertLessThanThousand(Math.floor(num / 1000000000)) + " billion ";
    num %= 1000000000;
  }
  if (num >= 1000000) {
    words += convertLessThanThousand(Math.floor(num / 1000000)) + " million ";
    num %= 1000000;
  }
  if (num >= 1000) {
    words += convertLessThanThousand(Math.floor(num / 1000)) + " thousand ";
    num %= 1000;
  }

  if (num > 0) {
    words += convertLessThanThousand(num);
  }

  return words.trim();
}

// Function to convert an amount to words with USD currency
function convertAmountToWords(amount) {
  const dollars = Math.floor(amount);
  const cents = Math.round((amount - dollars) * 100);

  const dollarWord = numberToWords(dollars);
  const centWord = numberToWords(cents);

  let result = "";

  if (dollars !== 0) {
    result += dollarWord + " us dollars";
  }

  if (dollars !== 0 && cents !== 0) {
    result += " and ";
  }

  if (cents !== 0) {
    result += centWord + " cents";
  }

  return result.trim().toUpperCase();
}
