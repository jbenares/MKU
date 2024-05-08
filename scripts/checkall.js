function check_all(frm, chAll)
{
    comfList = document.forms[frm].elements['checkList[]'];

    checkAll = (chAll.checked)?true:false; // what to do? Check all or uncheck all.

    // Is it an array
    if (comfList.length) {
        if (checkAll) {
            for (i = 0; i < comfList.length; i++) {
                comfList[i].checked = true;
            }
        }
        else {
            for (i = 0; i < comfList.length; i++) {
                comfList[i].checked = false;
            }
        }
    }
    else {
        /* This will take care of the situation when your 
checkbox/dropdown list (checkList[] element here) is dependent on
            a condition and only a single check box came in a list.
        */
        if (checkAll) {
            comfList.checked = true;
        }
        else {
            comfList.checked = false;
        }
    }
    return;
}

function check_all2(frm, chAll)
{
    comfList = document.forms[frm].elements['checkList2[]'];

    checkAll = (chAll.checked)?true:false; // what to do? Check all or uncheck all.

    // Is it an array
    if (comfList.length) {
        if (checkAll) {
            for (i = 0; i < comfList.length; i++) {
                comfList[i].checked = true;
            }
        }
        else {
            for (i = 0; i < comfList.length; i++) {
                comfList[i].checked = false;
            }
        }
    }
    else {
        /* This will take care of the situation when your 
checkbox/dropdown list (checkList[] element here) is dependent on
            a condition and only a single check box came in a list.
        */
        if (checkAll) {
            comfList.checked = true;
        }
        else {
            comfList.checked = false;
        }
    }
    return;
}


// validate data
function submitIt(frmName)
{
var comfList = document.forms[frmName].elements["checkList[]"];

    if (comfList.length) {

        var selected = false;
         for (var i=0; i < comfList.length; i++) {

            if (comfList[i].checked) {
                selected = true;  // is anyone selected/checked.
                break;
            }
         }
         if (! selected) {
              alert('Please check your comfortability list.');
              return false;
         }
         else {
               document.forms[frmName].submit();
               return true;
         }
    }
    else {

        if (comfList.checked) {
            document.forms[frmName].submit();
            return true;
        }
        else {
            alert('Please check your comfortability list.');
            return false;
        }

        }
}