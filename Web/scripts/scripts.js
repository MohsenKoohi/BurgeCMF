
function priceSeparator(val)
{
	var val=""+val;
	var newVal="";
	var j=0;
	for(var i=val.length-1;i>=0;i--,j++)
	{
		newVal=val[i]+newVal;
		if(j%3==2 && j!=(val.length-1))
			newVal=","+newVal;
	}

	return newVal;
}