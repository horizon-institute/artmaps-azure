<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ArtMaps | Tate Galleries</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link type="text/css" rel=StyleSheet href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/black-tie/jquery-ui.css" />
<script type="text/javascript" src="http://maps.google.com/maps/api/js?key=AIzaSyBDotOtQIdRgtPB6GJnMwRfUEAoluvrdqk&sensor=false&libraries=places"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/jquery-ui.min.js"></script>
</head>
<body>
<script type="text/javascript">
$(function() {
    var ac = new google.maps.places.Autocomplete(document.getElementById("search"));
});
</script>
<input id="search" name="search" type="text" />
</body>
</html>