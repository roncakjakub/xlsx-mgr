function ajaxSearch(el,url){
  var string = el.value;
  var request = $.ajax({
  url: url+"/search",
  method: "POST",
  data: { string: string },
  dataType: "html"
});
 
request.done(function( msg ) {
 var body=($($.parseHTML(msg)));
 $("#searchAnswer").html(body);
});  
}

function uploadFileName(el){
  var files = el.files; 
  var name=files[0].name;
  for (var i = 1; i < files.length; i++)
    name+=", "+files[i].name;    
  $("form .names span").text(name);
}
function scrollDown(inId){$('html, body').animate({scrollTop:$(inId).offset().top-90},1000);}

function delCheck(el)
{
        if(window.confirm("Skutočne chceš odstrániť tento príspevok?")){
            el.form.submit();
        }
        else
            return false;
    }

function delformsubmit() {
    if($('input.chck').is(':checked')){
    if(window.confirm("Skutočne chceš odstrániť tento album?")){
    $("#delForm").submit();   
    }
    else
        return false;
}
else alert("Nevybral si žiadnu položku.")
}

function formhash(form, password) {
  console.log(form);
    // Create a new element input, this will be our hashed password field. 
    var p = document.createElement("input");
 
    // Add the new element to our form. 
    form.appendChild(p);
    p.name = "p";
    p.type = "hidden";
    p.value = hex_sha512(password.value);
 
    // Make sure the plaintext password doesn't get sent. 
    password.value = "";
 
    // Finally submit the form. 
    form.submit();
}
 
function regformhash(form, email, password, conf) {
     // Check each field has a value
    if (  email.value == ''     || 
          password.value == ''  || 
          conf.value == '') {
 
        alert('You must provide all the requested details. Please try again');
        return false;
    }
 
    // Check that the password is sufficiently long (min 6 chars)
    // The check is duplicated below, but this is included to give more
    // specific guidance to the user
    if (password.value.length < 6) {
        alert('Passwords must be at least 6 characters long.  Please try again');
        form.password.focus();
        return false;
    }
 
    // At least one number, one lowercase and one uppercase letter 
    // At least six characters 
 
    var re = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}/; 
    if (!re.test(password.value)) {
        alert('Passwords must contain at least one number, one lowercase and one uppercase letter.  Please try again');
        return false;
    }
 
    // Check password and confirmation are the same
    if (password.value != conf.value) {
        alert('Your password and confirmation do not match. Please try again');
        form.password.focus();
        return false;
    }
 
    // Create a new element input, this will be our hashed password field. 
    var p = document.createElement("input");
 
    // Add the new element to our form. 
    form.appendChild(p);
    p.name = "p";
    p.type = "hidden";
    p.value = hex_sha512(password.value);
 
    // Make sure the plaintext password doesn't get sent. 
    password.value = "";
    conf.value = "";
 
    // Finally submit the form. 
    form.submit();
    return true;
}

function ajaxPreview(poradie,area) {
var oslovenie=poleJSON[poradie][area].nameArr[0];
var nazov=poleJSON[poradie].fileName;
var content=poleJSON[poradie][area].content;
var cena=poleJSON[poradie][area].cena;
 var request = $.ajax({
  url: "/public/ajax/previewMail.php",
  data: {nazov: nazov,oslovenie: oslovenie,content: content},
  method: "POST",
  dataType: "html"
});
 
request.done(function( msg ) {
 var body=($($.parseHTML(msg)));
 $("#previewMailModal .modal-body").html(body);
});
} 

function ajaxInvestFiles(poradie,area, permitAble) {
var subory=poleJSON[poradie][area].paymentFiles;
var nazov=poleJSON[poradie][area].nazov;
 var request = $.ajax({
  url: "/public/ajax/investFiles.php",
  data: {subory:subory, nazov: nazov,area: (area+1),permitAble: permitAble},
  method: "POST",
  dataType: "html"
});
 
request.done(function( msg ) {
 var body=($($.parseHTML(msg)));
 $("#investFilesModal .modal-body").html(body);
});
} 

function viewXLSXModal(poradie,area) {
var modal=$("#XLSXModal");
          /***********************************
              Deklarovanie
          **********************************/
var nazov=poleJSON[poradie][area].nazov;
var enddate=poleJSON[poradie][area].enddate;
var email=poleJSON[poradie][area].email;
var name=poleJSON[poradie][area].name;
var subory=poleJSON[poradie][area].paymentFiles;
var notif=poleJSON[poradie][area].notif;
var nazvySuborov="",temp;
if (!subory) nazvySuborov="Zatiaľ žiadne";
else
for(var i=0;i<subory.length; i++){
  temp=subory[i].split("/");
  nazvySuborov+="<a href='/download?area="+(area+1)+"&name="+nazov+"&fileNO="+(i+1)+"'>"+temp[temp.length - 1]+"</a><br>";
}
var dateDiff=poleJSON[poradie][area].dateDiff;

if(dateDiff>30)
  dateDIffStatus="Pred inicializáciou";

if (subory)
  dateDIffStatus="Prijaté potvrdenia platieb";
else if(dateDiff<=30&&dateDiff>0)
  dateDIffStatus="Odoslaný inic. email";
else if(dateDiff<=0)
  dateDIffStatus="Expirovaný "+Math.abs(Math.floor(dateDiff))+" dní.";

          /***********************************
                  Priredenie
          **********************************/
$(modal).find(".modal-title").html("<span class='clr-org font-weight-bold'>"+nazov+"</span>");
console.log(poleJSON[poradie]);
$(modal).find("#infoModalDate").html(enddate);
$(modal).find("#infoModalEmail").html(email);
$(modal).find("#infoModalName").html(name);
$(modal).find("#infoModalNotif").html(notif);
$(modal).find("#infoModalFiles").html(nazvySuborov);
$(modal).find("#infoModaldateDIff").html(dateDIffStatus);
$(modal).find("#infoModalDownloadButton").attr("href", '/download?area='+(area+1)+'&name='+nazov);
$(modal).find('#infoModalDeleteForm [name="xlsxID"]').val(poradie+1);
$(modal).find('#infoModalDeleteForm [name="area"]').val(area+1);
} 

function expandedNavOn() {
    $('#hiddenNav').animate({
    width : '250%'
  }, 1000);
    $('#hiddenNav .arrow').one("click", expandedNavOff);
}
function expandedNavOff() {
    $('#hiddenNav').animate({
    width : '100%'
  }, 1000);
    $('#hiddenNav .arrow').one("click", expandedNavOn);
}


    //$(nav).toggleClass("openedMenu").toggleClass("col-md-1").toggleClass("col-md-2");;
    ///$(main).toggleClass("col-md-11").toggleClass("col-md-10").toggleClass("offset-md-1").toggleClass("offset-md-2");
function ajax(ID) {
 var request = $.ajax({
  url: "public/ajax.php",
  data: {nazov: nazov,oslovenie: oslovenie,popis: popis},
  method: "POST",
  dataType: "html"
});
 
request.done(function( msg ) {
 var body=($($.parseHTML(msg)));
 $("#previewMailModal .modal-body").html(body);
});
} 