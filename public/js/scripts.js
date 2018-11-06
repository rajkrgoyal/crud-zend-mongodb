var hidden = true;
function toggleNewPass(e){
  if(e.checked){
    document.querySelector("#new_pass").style.display = "block";
    document.querySelector("#new_pass input").required = true;
    document.querySelector("#old_pass").style.display = "block";
    document.querySelector("#old_pass  input").required = true;
    hidden = false;
  }
  else{
    document.querySelector("#new_pass").style.display = "none";
    document.querySelector("#new_pass input").required = false;
    document.querySelector("#old_pass").style.display = "none";
    document.querySelector("#old_pass  input").required = false;
    hidden = true;
  }
}

function passValidator(event){
  if(hidden){
    return;
  }
  let opass = document.querySelector("#opwd").value;
  let npass = document.querySelector("#npwd").value;
  if(opass.length<6  || npass.length<6){
    alert("password length should be greater than or equal to 6");
    event.preventDefault();
  }
}

function passValidator(event){
    let pass = document.querySelector("#pwd").value;
    if(pass.length<6){
      alert("password length should be greater than or equal to 6");
      event.preventDefault();
    }
  }