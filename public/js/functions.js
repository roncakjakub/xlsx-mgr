function ukazAlbum(el){
var albumID = $(el).data( "id" );
var request = $.ajax({
  url: "../gallery/album.php",
  method: "POST",
  data: { ID : albumID },
  dataType: "html"
});
 
request.done(function( msg ) {
 var heading =  $($.parseHTML(msg)).filter("#heading").text(); 
 $("#exampleModalLabel").text(heading);
 var body=($($.parseHTML(msg)).filter("main"));
 $("#modal-body").html(body);
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
var oslovenie=poleJSON[poradie]["fileData"][area].name;
var nazov=poleJSON[poradie].fileName;
var popis=poleJSON[poradie]["fileData"][area].popis;
var cena=poleJSON[poradie]["fileData"][area].cena;
 var request = $.ajax({
  url: "/public/ajax/previewMail.php",
  data: {nazov: nazov,oslovenie: oslovenie,popis: popis,cena:cena},
  method: "POST",
  dataType: "html"
});
 
request.done(function( msg ) {
 var body=($($.parseHTML(msg)));
 $("#previewMailModal .modal-body").html(body);
});
} 

function ajaxInvestFiles(poradie,area, permitAble) {
var subory=poleJSON[poradie]["fileData"][area].paymentFiles;
var nazov=poleJSON[poradie].fileName;
var xlsxs=poleJSON[poradie].fileAdr;
 var request = $.ajax({
  url: "/public/ajax/investFiles.php",
  data: {subory:subory, nazov: nazov,xlsxs: xlsxs,area: (area+1),permitAble: permitAble},
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
var nazov=poleJSON[poradie].fileName;
var enddate=poleJSON[poradie]["fileData"][area].enddate;
var email=poleJSON[poradie]["fileData"][area].email;
var name=poleJSON[poradie]["fileData"][area].name;
var subory=poleJSON[poradie]["fileData"][area].paymentFiles;
var notif=poleJSON[poradie]["fileData"][area].notif;
var nazvySuborov="",temp;
if (!subory) nazvySuborov="Zatiaľ žiadne";
else
for(var i=0;i<subory.length; i++){
  temp=subory[i].split("/");
  if (temp[temp.length - 1]!="downloadedFiles.xml")
  nazvySuborov+="<a href='/download?area="+(area+1)+"&name="+nazov+"&fileNO="+i+"'>"+temp[temp.length - 1]+"</a><br>";
}
var dateDiff=poleJSON[poradie]["fileData"][area].dateDiff;

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
$(modal).find("#infoModalDate").text(enddate);
$(modal).find("#infoModalEmail").text(email);
$(modal).find("#infoModalName").text(name);
$(modal).find("#infoModalNotif").text(notif);
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