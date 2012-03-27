formServiceUrl = "http://form.service.test/rest/index";

$(document).ready(function () {
    getForm(6);
});


var formElement = $("<form type='' ></form>");

getForm = function ($id) {
    $.ajax({
        cache: true,
        url: formServiceUrl + "/" + $id,
        data: "",
        type: "GET",
        contentType: "application/javascript",
        dataType: "jsonp",
        error: function () {
            alert("failed to fetch form with id:" + $id + "!");
        },
        success: function (datalist) {
        	$();
            $(datalist).each(function(i, data) {
            	buildElement(data);
            });
        }
    });
};

var buildElement = function(data) {
	var el = null;
	var DL = $("<dl></dl>");
	switch(data.elementType) {
		case 'Label':
			el = $("<div class='" + data.name + "'>" + data.label + "</div>");
			break;
		case 'Seperator':
			el = $("<div class='seperator'></div>");
			break;
		case 'Checkbox':
			el = $("<input type='checkbox' name='" + data.name + "' value='' />");
			break;
		case 'MultiCheckbox':
			el = $("<div class='form-checkbox-group'></div>");
			$(data.options).each(function(i, op) {
				var opEl = $("<input id='option_" + op.id + "' type='checkbox' name='" + data.name + "[]' value='" + op.id + "' />");
				var laEl = $("<label for='option_" + op.id + "'>" + op.label + "</label>");
				el.append(opEl);
				el.append(laEl);
			});
			break;
		case 'Multiselect':
			el = $("<select name='" + data.name + "' multiple='multiple'></select>");
			$(data.options).each(function(i, op) {
				var opEl = $("<option value='" + op.id + "'>" + op.label + "</option>");
				el.append(opEl);
			});
			break;
		case 'Radio':
			el = $("<div class='form-radio-group'></div>");
			$(data.options).each(function(i, op) {
				var opEl = $("<input id='option_" + op.id + "' type='radio' name='" + data.name + "' value='" + op.id + "' />");
				var laEl = $("<label for='option_" + op.id + "'>" + op.label + "</label>");
				el.append(opEl);
				el.append(laEl);
			});
			break;
		case 'Select':
			el = $("<select name='" + data.name + "'></select>");
			$(data.options).each(function(i, op) {
				var opEl = $("<option value='" + op.id + "'>" + op.label + "</option>");
				el.append(opEl);
			});
			break;
		case 'Text':
			el = $("<input type='text' name='" + data.name + "' value='' />");
			break;
		case 'Textarea':
			el = $("<textarea name='" + data.name + "'></textarea>");
			break;
	}
	if(data.required == 1) {
		DL.append("<dt>" + data.label + " <span class='star'>*</span></dt>");
	} else {
		DL.append("<dt>" + data.label + "</dt>");
	}
	
	var DD = $("<dd></dd>");
	DD.append(el);
	DL.append(DD);
	if(data.desc != "" && data.desc != null) {
		DL.append("<dd class='desc'>" + data.desc + "</dd>");
	}
	$('body').append(DL);
} 
