"use strict";
var KTPasswordResetGeneral = (function () {
  var t, e, i;
  return {
    init: function () {
      (t = document.querySelector("#kt_password_reset_form")),
        (e = document.querySelector("#kt_password_reset_submit")),
        (i = FormValidation.formValidation(t, {
          fields: {
            email: {
              validators: {
                notEmpty: { message: "Email address is required" },
                emailAddress: {
                  message: "The value is not a valid email address",
                },
              },
            },
          },
          plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap: new FormValidation.plugins.Bootstrap5({
              rowSelector: ".fv-row",
              eleInvalidClass: "",
              eleValidClass: "",
            }),
          },
        })),
        e.addEventListener("click", function (o) {
          o.preventDefault();
          i.validate().then(function (status) {
            if (status === "Valid") {
              e.setAttribute("data-kt-indicator", "on");
              e.disabled = true;
              // Serialize form data
              const formData = new FormData(t);
              // Perform AJAX request
              fetch('/forgot-password', {
                method: 'POST',
                body: formData
              })
              .then(function (response) {
                if (!response.ok) {
                  throw new Error("Network response was not ok");
                }
                return response.json();
              })
              .then(function (data) {
                // Handle success response
                Swal.fire({
                  text: data.message, // Assuming your response has a 'message' field
                  icon: "success",
                  buttonsStyling: false,
                  confirmButtonText: "Ok, got it!",
                  customClass: { confirmButton: "btn btn-primary" },
                }).then(function () {
                  t.querySelector('[name="email"]').value = "";
                });
              })
              .catch(function (error) {
                // Handle error
                Swal.fire({
                  text: "Sorry, an error occurred. Please try again.",
                  icon: "error",
                  buttonsStyling: false,
                  confirmButtonText: "Ok, got it!",
                  customClass: { confirmButton: "btn btn-primary" },
                });
              })
              .finally(function () {
                e.removeAttribute("data-kt-indicator");
                e.disabled = false;
              });
            } else {
              // Handle validation errors
              Swal.fire({
                text: "Sorry, looks like there are some errors detected, please try again.",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: { confirmButton: "btn btn-primary" },
              });
            }
          });
        });
    },
  };
})();
KTUtil.onDOMContentLoaded(function () {
  KTPasswordResetGeneral.init();
});
