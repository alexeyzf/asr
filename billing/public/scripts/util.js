/**
 * Js util functions
 *
 * @author marat
 */

/**
 * Redirects browser to specified url
 */
function redirect(url)
{
    window.location.href = url;
}

/**
 * Addes template div html to div with replace COUNTER to random number
 */
function addDiv(divID, templateDivID)
{
    var templateHtml = $('#' + templateDivID).html();
    var randomRow = Math.round(1000 * Math.random());
    var newElement = templateHtml.replace(/COUNTER/ig, randomRow);

    $('#' + divID).append(newElement);
}

function addspan()
{
    var randomRow = Math.round(1000 * Math.random());
    var str = "<span id ='sp_" + randomRow + "'>" +
        "<br/><input  type='text' name='some[]' size=30 />" +
        "<img class='link' src='/images/icons/delete.png' onclick=\"removeWithConfirm('sp_" + randomRow + "')\" />" +
        "</span>";

    $("#sp_id").append(str);

}


function drop()
{
	if(confirm('You really wish to terminate the contract from the client'))
	{
		document.mainform.submit();
	}
	else
	{
		return false;
	}
}



function removeWithConfirm(elementID)
{
    if (confirm('Вы действительно хотите удалить?'))
    {
        $('#' + elementID).remove();
    }
}

function openIframe(url)
{
	window.open(url,'iframe','width=650,height=320,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,copyhistory=no,resizable=no');
}

function chosePoint(pointID, table)
{
	$.php('/ajax/show-point-info', {
		point_id: pointID,
		table: table
	});
}

var rowPattern =
    '<tr id="{0}_row_{1}">' +
        '{2}' +
    '</tr>';
var xButtonPattern =
    '<td rowspan="2">' +
        '<img class="link" src="/images/icons/delete.png" onclick="$(\'#{0}_row_{1}\').remove()" alt="Удалить" />' +
    '<td>';
var uplaodButtonPattern =
    '<input type="file" name="document_file_{0}"/>';

var counter = 1;
function addRow(element)
{
    ++counter;
    var rowHtml = $(String.format('#{0}_row', element)).html();
    var resultRowData = rowHtml.
        replace('<!--put-x-button-->', String.format(xButtonPattern, element, counter)).
        replace(/<!--remove-start-->(?:.|\n|\r)*<!--remove-finish-->/gm, '').
        replace('<input name="document_file" type="file">', String.format(uplaodButtonPattern, counter));
    var resultRow = String.format(rowPattern, element, counter, resultRowData);
    $(String.format('#{0}_table > tbody:last', element)).append(resultRow);
}
