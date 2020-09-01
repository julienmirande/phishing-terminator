var url = "";

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

function getSourceAsDOM(url)
{
  xmlhttp=new XMLHttpRequest();
  xmlhttp.open("GET",url,false);
  xmlhttp.send();
  parser=new DOMParser();
  return parser.parseFromString(xmlhttp.responseText,"text/html");
}

function openWindowWithPost(url, data) {
    var form = document.createElement("form");
    form.target = "_blank";
    form.method = "POST";
    form.action = url;
    form.style.display = "none";

    var input = document.createElement("input");
    input.type = "hidden";
    input.name = "url";
    input.value = data["url"];
    form.appendChild(input);


    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}


document.getElementById("btn").addEventListener("click", function(){
  openWindowWithPost("http://localhost:8000/report.php", {
    url: url,
});
});


//Send a message
sendMessage();

//Get message from background page
chrome.runtime.onMessage.addListener(function(request, sender, sendResponse) {
  //Alert the message
  //Construct & send a response
  sendResponse({
    response: "Message received"
  });
});

//Send message to background page
function sendMessage() {
  //Construct & send message

  chrome.runtime.sendMessage({
    method: "postList",
    post_list: "ThePostList",
    reason: "ask_for"
  }, function(response) {

    url = response.response[1];

    if(response.response[0] == "trusted"){
      document.getElementById("trust_image").src = "skin/tick.png";

    }else {
      document.getElementById("trust_image").src = "skin/risk.png";
      document.getElementById("trust_text").innerHTML = "Unknown website";

    }

  });
}
