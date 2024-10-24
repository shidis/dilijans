/* init */

window.onload = function () {onLoad()};

onLoad = function () {
	initCalculator();
	initDiscCalculator();
}


/* calculator */

initCalculator = function() {
	oCalculator = {
		'oldWidth' : document.getElementById('oldWidth'),
		'oldDiameter' : document.getElementById('oldDiameter'),
		'oldProfile' : document.getElementById('oldProfile'),
		'newWidth' : document.getElementById('newWidth'),
		'newDiameter' : document.getElementById('newDiameter'),
		'newProfile' : document.getElementById('newProfile'),

		'oldL' : document.getElementById('oldL'),
		'newL' : document.getElementById('newL'),
		'deltaL' : document.getElementById('deltaL'),

		'oldH' : document.getElementById('oldH'),
		'newH' : document.getElementById('newH'),
		'deltaH' : document.getElementById('deltaH'),

		'oldD' : document.getElementById('oldD'),
		'newD' : document.getElementById('newD'),
		'deltaD' : document.getElementById('deltaD'),

		'oldDD' : document.getElementById('oldDD'),
		'newDD' : document.getElementById('newDD'),
		'deltaDD' : document.getElementById('deltaDD'),

        'newRS' : document.getElementById('newRS'),
        'deltaRS' : document.getElementById('deltaRS')
	}

	applyCalculateEvent(oCalculator.oldWidth);
	applyCalculateEvent(oCalculator.oldDiameter);
	applyCalculateEvent(oCalculator.oldProfile);
	applyCalculateEvent(oCalculator.newWidth);
	applyCalculateEvent(oCalculator.newDiameter);
	applyCalculateEvent(oCalculator.newProfile);

	doCalculate();
}

applyCalculateEvent = function (nNode) {
	nNode.onchange = function () {doCalculate();};
}



doCalculate = function() {
	var iOldL = oCalculator.oldWidth.value;
	var iNewL = oCalculator.newWidth.value;

	var iOldD = Math.round(oCalculator.oldDiameter.value * 25.4);
	var iNewD = Math.round(oCalculator.newDiameter.value * 25.4);

	var iOldDD = Math.round(oCalculator.oldWidth.value*oCalculator.oldProfile.value*0.02 + oCalculator.oldDiameter.value*25.4);
	var iNewDD = Math.round(oCalculator.newWidth.value*oCalculator.newProfile.value*0.02 + oCalculator.newDiameter.value*25.4);

	var iOldH = Math.round((iOldDD - iOldD)/2);
	var iNewH = Math.round((iNewDD - iNewD)/2);
	var iSpeedCoeff = iNewDD/iOldDD;

	setTextValue(oCalculator.oldL, iOldL);
	setTextValue(oCalculator.newL, iNewL);
	setTextValue(oCalculator.deltaL, iNewL - iOldL);

	setTextValue(oCalculator.oldH, iOldH);
	setTextValue(oCalculator.newH, iNewH);
	setTextValue(oCalculator.deltaH, iNewH - iOldH);

	setTextValue(oCalculator.oldD, iOldD);
	setTextValue(oCalculator.newD, iNewD);
	setTextValue(oCalculator.deltaD, iNewD - iOldD);

	setTextValue(oCalculator.oldDD, iOldDD);
	setTextValue(oCalculator.newDD, iNewDD);
	setTextValue(oCalculator.deltaDD, iNewDD - iOldDD);

    var newRS=Math.round(60*iSpeedCoeff * 10)/10;
    setTextValue(oCalculator.newRS, newRS);
    setTextValue(oCalculator.deltaRS, Math.round((newRS - 60)*10)/10);

}




/* disc */

initDiscCalculator = function () {
	if (
		document.getElementById
		&& document.getElementById('tireWidth')
		&& document.getElementById('tireDiameter')
		&& document.getElementById('tireProfile')
		&& document.getElementById('discDiameter')
		&& document.getElementById('discWidthMin')
		&& document.getElementById('discWidthMax')
	) {
		oDiscCalculator = {
			'tireWidth' : document.getElementById('tireWidth'),
			'tireDiameter' : document.getElementById('tireDiameter'),
			'tireProfile' : document.getElementById('tireProfile'),
			'discDiameter' : document.getElementById('discDiameter'),
			'discWidthMin' : document.getElementById('discWidthMin'),
			'discWidthMax' : document.getElementById('discWidthMax')
		}
		applyCalculateDiscEvent(oDiscCalculator.tireWidth);
		applyCalculateDiscEvent(oDiscCalculator.tireDiameter);
		applyCalculateDiscEvent(oDiscCalculator.tireProfile);

		doCalculateDisc();
	}
}


applyCalculateDiscEvent = function (nNode) {
	nNode.onchange = function () {doCalculateDisc();};
}

doCalculateDisc = function() {
	if (oDiscCalculator) {
		var iWidth = oDiscCalculator.tireWidth.value;
		var iProfile = oDiscCalculator.tireProfile.value;
		var iDiameter = oDiscCalculator.tireDiameter.value;

		iWidthMin = (Math.round(((iWidth*((iProfile < 50) ? 0.85 : 0.7))*0.03937)*2))/2;
		iWidthMax = (iWidthMin+1.5);

		setTextValue(oDiscCalculator.discDiameter, iDiameter);
		setTextValue(oDiscCalculator.discWidthMin, iWidthMin);
		setTextValue(oDiscCalculator.discWidthMax, iWidthMax);
	}
}


setTextValue = function (nNode, sValue) {
	sValue = String(sValue);
	sValue = sValue.replace(/\./, ',');
	nNode.innerHTML = sValue;
}


function t1 (){
    $('#t1').html('<a href="'+turl+'&p3='+$('#oldWidth').val()+'&p2='+$('#oldProfile').val()+'&p1='+$('#oldDiameter').val()+'">купить шины '+$('#oldWidth').val()+'/'+$('#oldProfile').val()+' R'+$('#oldDiameter').val()+'</a>');
}
function t2 (){
    $('#t2').html('<a href="'+turl+'&p3='+$('#newWidth').val()+'&p2='+$('#newProfile').val()+'&p1='+$('#newDiameter').val()+'">купить шины '+$('#newWidth').val()+'/'+$('#newProfile').val()+' R'+$('#newDiameter').val()+'</a>');
}
$(document).ready(function(){

    t1();
    t2();

    $('#oldWidth, #oldProfile, #oldDiameter').change(t1);
    $('#newWidth, #newProfile, #newDiameter').change(t2);
});

