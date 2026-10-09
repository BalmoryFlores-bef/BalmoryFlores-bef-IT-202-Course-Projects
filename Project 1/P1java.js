let representatives = [
	{
    	fName: "John",
    	lName: "Moving",
    	pwd: "@Pswrd1",
    	identity: "1234",
    	phoNum: "123-456-7890 ext 123",
    	Email: "john.doe@example.com"
  	}, 
	{
   	fName: "Jane",
    	lName: "Smith",
    	pwd: "#Scure9",
    	identity: "5678",
    	phoNum: "987-654-3210 ext 456",
    	Email: "jane.smith@jr.com"
  	},
  	{
    	fName: "Bob",
    	lName: "Beb",
    	pwd: "!Watch0",
    	identity: "8912",
    	phoNum: "102-283-3267 ext 901",
    	Email: "Bob87@jib.com"
  	},
  	{
    	fName: "Yellow",
    	lName: "Blue",
    	pwd: "%cowB0y",
    	identity: "3344",
    	phoNum: " 897-675-4567 ext ",
    	Email: "YB@loew.com"
  	},
  	{
    	fName: "Maria",
    	lName: "Lopez",
    	pwd: "^9oPop",
    	identity: "8912",
    	phoNum: "102-283-3267 ext 901",
    	Email: "Ml@lop.com"
  	},
  	{
    	fName: "Blingo",
    	lName: "Blizzard",
    	pwd: "&Bb9",
    	identity: "9012",
    	phoNum: "872-833-4589 ext 991",
    	Email: "BB@BB.com"
  	},
	{
	fName: "Five",
    	lName: "Fifth",
    	pwd: "%5Five",
    	identity: "5555",
    	phoNum: "555-555-5555 ext 556",
    	Email: "Five@E5.com"
  	},
	{
    	fName: "Jale",
    	lName: "Jmith",
    	pwd: "%Jur8",
    	identity: "1010",
    	phoNum: " 101-101-1010 ext 999",
    	Email: "JS@Jletter.com"
  	},
	{
	fName: "Silva",
    	lName: "Longjohn",
    	pwd: "*rom9S",
    	identity: "4739",
    	phoNum: " 967-428-2398 ext 238",
    	Email: "SL@JBrom.com"
  	},
	{
	fName: "Rombus",
    	lName: "Blombus",
    	pwd: "$Bus85",
    	identity: "9420",
    	phoNum: " 347-988-2468 ext 848",
    	Email: "Rb@mbus.com"
  	},
	];

function validate(){
	let fName = document.getElementById("fName");
	let lName = document.getElementById("lName");
	let pwd = document.getElementById("pwd");
	let identity = document.getElementById("identity");
	let phoNum = document.getElementById("phoNum");
	let Email = document.getElementById("Email");
	let checkbox = document.getElementById("chBox");

	if (fName.value.length === 0) {
		alert("Please enter first name");
		fName.focus();
		fName.classList.add("error");
		//Issues return false to stop the function where it is, and return only one alert at a time
		return false;
	}

	if (!/^[A-Za-z]+$/.test(fName.value)) {
		alert("User's first name should only consist of alphabetical characters");
		
		fName.focus();
		return false;
	}

	if (lName.value.length === 0) {
		alert("Please enter last name");
		lName.focus();
		return false;
	}

	if (!/^[A-Za-z]+$/.test(lName.value)) {
		alert("User's last name should only consist of alphabetical characters");
		lName.focus();
		return false;
	}

	if (pwd.value.length === 0) {
		alert("Please enter password");
		pwd.focus();
		return false;
	}

	if (!/^(?=[^A-Za-z0-9])(?=.*[A-Z])(?=.*\d).{1,7}$/.test(pwd.value)){
		alert("Password must be at most, 7 characters long, start with a special character, contain at least one uppercase letter, and at least one number");
    		pwd.focus();
    		return false;
	}

	if (identity.value.length === 0) {
		alert("Please enter ID");
		identity.focus();
		return false;
	}
	
	if (!/^\d{4}$/.test(identity.value)) {
		alert("Moving Representative's ID should only be 4 digits in length and consist of only numbers");
		identity.focus();
		return false;
	}

	if (phoNum.value.length === 0){
		alert("Please enter phone number");
		phoNum.focus();
		return false;
	}
	
	if (!/^\d{3}[- ]\d{3}[- ]\d{4} ext \d{3}$/.test(phoNum.value)) {
		alert("Phone number must contain 10 digits separated by spaces or dashes followed by representative's extionsion number.");
		phoNum.focus();
		return false;
	}

	if (checkbox.checked){
		if(Email.value.length === 0){
			alert("Please enter email address");
			Email.focus();
			return false;
		}

		if (!/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,5}$/.test(Email.value)) {
			alert("The email address must contain an @ followed by a period and an email domain that consists of 2 to 5 characters.");
			Email.focus();
			return false;
		}
	}


//Save user inputs into an array 
	let inputValues = [
    		fName.value,
    		lName.value,
    		pwd.value,
    		identity.value,
    		phoNum.value,
    		Email.value
  		];
	return verify(inputValues, checkbox.checked);
}


function verify(input, isEmailRequired){
	//The chosen transaction
	let selectedTransaction = document.querySelector(".optClass").value;		
	//user can input dashes or spaces, normalization allows comparison regardless of what was used
	let normalizedUserPhone = input[4].replace(/[- ]/g, "");


	//loop through representatives array
	for (let i = 0; i < representatives.length; i++) {
		//Iterate through each representative starting from index i
		let rep = representatives[i];
		//normalized saved rep phone number for simple comparison
		let normalizedRepPhone = rep.phoNum.replace(/[- ]/g, "");

	//if values are found to not match, break out of loop and move to the next
		if (input[0] !== rep.fName) continue;
    		if (input[1] !== rep.lName) continue;
    		if (input[2] !== rep.pwd) continue;
    		if (input[3] !== rep.identity) continue;
    		if (normalizedUserPhone !== normalizedRepPhone) continue;

		if (isEmailRequired && input[5] !== rep.Email) {
      			continue;
    		}
		//Successful validation and verication
		alert("Welcome " + rep.fName + " " + rep.lName + "! You have requested: " 		+ selectedTransaction);
    		return true;
	}
	//User is not on the system
	alert("An account for " + input[0] + " " + input[1] + " cannot be found.");
  	return false;
}


function resetForm(){
	document.getElementById("fName").value = "";
	document.getElementById("lName").value = "";
	document.getElementById("pwd").value = "";
	document.getElementById("identity").value = "";
	document.getElementById("phoNum").value = "";
	document.getElementById("Email").value = "";

	document.getElementById("chBox").checked = false;
	}


//Toggle function to show/hide passwords
function toggle() {
	var display = document.getElementById("pwd");

//Exactly the same as week 5, password hidden automatically, then has its type changed to text and back when hidden again
		if (display.type == "password"){
			display.type = "text";
		} else {
			display.type = "password";
		}
}
