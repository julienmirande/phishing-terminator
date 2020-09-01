function getUrlParameter(sParam) {
  var sPageURL = decodeURIComponent(window.location.search.substring(1)),
  sURLVariables = sPageURL.split('&'),
  sParameterName,
  i;

  for (i = 0; i < sURLVariables.length; i++) {
    sParameterName = sURLVariables[i].split('=');

    if (sParameterName[0] === sParam) {
      return sParameterName[1] === undefined ? true : sParameterName[1];
    }
  }
};



function checkSession() {
  var statut = getUrlParameter('statut');

  if (statut == "connected")
  {
    var x = document.getElementById("snackbar");
    x.className = "show";
    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
  }
}

var modal = document.getElementById('myModal');
var img = document.getElementById('myImg');
var modalImg = document.getElementById("img01");
var captionText = document.getElementById("caption");
var idReport = getUrlParameter('idReport');
if (idReport)
{
  img.onclick = function(){
    modal.style.display = "block";
    modalImg.src = this.src;
    captionText.innerHTML = this.alt;
  }
}
if (idReport)
{
  var span = document.getElementsByClassName("close")[0];
  span.onclick = function() {
    modal.style.display = "none";
  }
}

function checkAllFilled()
{

  var reasonFilled = $("input[type='radio'].reason").is(':checked');
  var signsFilled = $('#sign1').is(":checked") || $('#sign2').is(":checked") || $('#sign3').is(":checked")|| $('#sign4').is(":checked") || $('#sign5').is(":checked") || $('#sign6').is(":checked");
  if (!reasonFilled && !signsFilled)
  {
    return "both";
  }
  else if  (!reasonFilled)
  {
    return "reasonFilled";
  }
  else if (!signsFilled) {
    return "signsFilled";
  }
  else {
    return "ok";
  }

}

function redirectPost(url, data) {
    var form = document.createElement('form');
    document.body.appendChild(form);
    form.method = 'post';
    form.action = url;
    for (var name in data) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = data[name];
        form.appendChild(input);
    }
    form.submit();
}

$('.submit').on('click',function() {

    var reasons;
    if($("input[type='radio'].reason").is(':checked')) {
    reasons = $("input[type='radio'].reason:checked").val();
  }

  var signs = [];
  if ($('#sign1').is(":checked"))
  {
      signs.push($('#sign1').val());
  }

  if ($('#sign2').is(":checked"))
  {
      signs.push($('#sign2').val());
  }

  if ($('#sign3').is(":checked"))
  {
      signs.push($('#sign3').val());
  }

  if ($('#sign4').is(":checked"))
  {
      signs.push($('#sign4').val());
  }

  if ($('#sign5').is(":checked"))
  {
      signs.push($('#sign5').val());
  }

  if ($('#sign6').is(":checked"))
  {
      signs.push($('#sign6').val());
  }


  var result = checkAllFilled();
    $("#containerReason").css("border-color","black");
    $("#containerSigns").css("border-color","black");
  if ( result === "both")
  {
    $("#containerReason").css("border-color","red");
    $('<h5 style="color: red" class="temp"> You must select a reason ! </h5>').insertAfter($("#containerReason label:last-child"));
    $("#containerSigns").css("border-color","red");
    $('<h5 style="color: red" class="temp"> You must select at least one sign ! </h5>').insertAfter($("#containerSigns div.row:last-child"));
  }

  if ( result === "reasonFilled")
  {
    $("#containerReason").css("border-color","red");
    $('<h5 style="color: red"> You must select a reason ! </h5>').insertAfter($("#containerReason label:last-child"));
  }

  if (result === "signsFilled") {
    $("#containerSigns").css("border-color","red");
    $('<h5 style="color: red"> You must select  at least one sign ! </h5>').insertAfter($("#containerSigns div.row:last-child"));
  }

  var message = "";
  if ( $("#textarea").val() != "")
  {
     message = $("#textarea").val();
  }

  var url = $("#url").val(); ;

  var email = $("#emailInput").val();

  var picture =  $("#myImg").attr('alt');



  if ( result === "ok"){
    $("#containerReason").css("border-color","black");
    $("#containerSigns").css("border-color","black");
    redirectPost("http://localhost:8000/report.php", { reasons: String(reasons),signs: signs,url: String(url),message: String(message) , email:email ,picture:picture});

    //alert("Reason = " + reason + "Signs = "  );
  }
});
