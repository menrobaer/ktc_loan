<style>
  .colored-toast .swal2-title {
    color: #fff !important;
  }
</style>

<?php
$this->registerJS('
var Toast = Swal.mixin({
    toast: true,
    position: "bottom-end",
    showConfirmButton: false,
    timer: 5000,
    iconColor: "#fff",
    background: "#222230",
    customClass: {
        container: "colored-toast",
    }
});
');
$session = Yii::$app->session;
if ($session->hasFlash('success')) {
  $toast_text = $session->getFlash('success');
  $this->registerJS("
    Toast.fire({
        icon: 'success',
        title: \"$toast_text\"
    });
");
} else if ($session->hasFlash('info')) {
  $toast_text = $session->getFlash('info');
  $this->registerJS("
    Toast.fire({
        icon: 'info',
        title: \"$toast_text\"
    });
");
} else if ($session->hasFlash('warning')) {
  $toast_text = $session->getFlash('warning');
  $this->registerJS("
    Toast.fire({
        icon: 'warning',
        title: \"$toast_text\"
    });
");
} else if ($session->hasFlash('error')) {
  $toast_text = $session->getFlash('error');
  $this->registerJS("
    Toast.fire({
        icon: 'error',
        title: \"$toast_text\"
    });
");
} else if ($session->hasFlash('question')) {
  $toast_text = $session->getFlash('question');
  $this->registerJS("
    Toast.fire({
        icon: 'question',
        title: \"$toast_text\"
    });
");
}
?>