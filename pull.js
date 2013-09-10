try
{
	xhr = new ActiveXObject("Microsoft.XMLHTTP");    // Trying Internet Explorer 
}
catch(e)    // Failed 
{
	xhr = new XMLHttpRequest();    // Other browsers.
}
