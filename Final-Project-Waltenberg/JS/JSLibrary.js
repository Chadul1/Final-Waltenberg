

//Creates a form ny assigning and returning a form with the inputted attributes. 
//Id for easy access and Identification when coding, action for return destinations upon submission,
//and method for returning   
export const createForm = (id, action, method) => {
    const form = document.createElement('form');
    form.id = id;
    form.action = action;
    form.method = method;
    return form;
};


// Adds a field to the inputted form. does not need form to already exist however.
export const addFormField = (form, field) => {
    form.appendChild(field); 
}

//Takes created form field object and adds it to the form inputted.
//In case the formId returns as null, the form isn't added. This is to make it easier for dynamic form development. 
//(Eg. A dropdown text field object is inputted to formId "form42". that object is attached to the form as a child object.
// The form will now always have a dropdown until it's removed.)
export const addFieldToForm = (formId, field) => {
    const form = document.getElementById(formId);
    if (form) {
        form.appendChild(field);
    } else {
        console.error(`Form with id '${formId}' not found.`);
    }
};


//Takes a created form and removes a field object from it. 
//In case the formId OR fieldName return null, it throws and error alerting the dev. 
//(Eg. The name of field "AccountPasswordBox" is inputted to "form42".
//If both of those are found. The field is removed from the Form) 
export const removeFieldFrom = (formId, fieldName) => {
    const form = document.getElementById(formId);
    if (form) {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (field) {
            form.removeChild(field.parentElement);
        } else {
            console.error(`Field with name '${fieldName}' not found in form.`);
        }
    } else {
        console.error(`Form with id '${formId}' not found.`);
    }
};


//Takes the inputted values and creates a text input and attached label where applicable. 
//Name and labelText are used for the content in the label as well as identifying the name of forms for submission. 
//Placeholder is the placeholder inside the input and the value is for identifying the initial default value for the input. 
//In case of (Optional inputs) that still require verification.  
export const createTextInput = (name, labelText, placeholder, value) => {
    const wrapper = document.createElement('div');
    if (labelText) {
        const label = document.createElement('label');
        label.htmlFor = name;
        label.textContent = labelText;
        wrapper.appendChild(label);
    }
    const input = document.createElement('input');
    Object.assign(input, {
        type: 'text',
        name,
        placeholder,
        value
    });
    wrapper.appendChild(input);
    return wrapper;
};



//Takes the inputted values and creates a password input and attached label where applicable. 
//Name and labelText are used for the content in the label as well as identifying the name of forms for submission. 
//Uses a placeholder value for the 
export const createPasswordInput = (name, labelText, placeholder) => {
    const wrapper = document.createElement('div');
    if (labelText) {
        const label = document.createElement('label');
        label.htmlFor = name;
        label.textContent = labelText;
        wrapper.appendChild(label);
    }
    //Creates an input with the type password. 
    //Required is set to true to ensure that the form isn't submitted without an inputted value here. 
    const input = document.createElement('input');
    Object.assign(input, {
        type: 'password',
        name,
        required: true,
        placeholder
    });    
    wrapper.appendChild(input);

    const errorMessage = document.createElement('small');
    errorMessage.classList.add('error-message'); // Add a class for styling
    errorMessage.style.color = 'red'; // Set initial style
    wrapper.appendChild(errorMessage); // Add error message element to wrapper

    //returns the field within a wrapper.
    return wrapper;
};



//Creates a Text area with space to type. 
//Name and labelText are used for the content in the label as well as identifying the name of forms for submission. 
export const createTextarea = (name, labelText, placeholder, value) => {
    const wrapper = document.createElement('div');
    if (labelText) {
        const label = document.createElement('label');
        label.htmlFor = name;
        label.textContent = labelText;
        wrapper.appendChild(label);
    }
    const textarea = document.createElement('textarea');
    Object.assign(textarea, {
        name,
        placeholder,
        value
    });
    wrapper.appendChild(textarea);
    return wrapper;
};


//Creates a dropdown menu with pick-able options. 
//Name and labelText are used for the content in the label as well as identifying the name of forms for submission. 
//The options parameter is used for adding the available inputs for the menu. 
//(Eg. A list with every U.S State in it is inputted, it is added to the dropdown, now the available options of the dropdown are the State values in the list.)
export const createDropdown = (name, options, labelText) => {
    const wrapper = document.createElement('div');
    if (labelText) {
        const label = document.createElement('label');
        label.htmlFor = name;
        label.textContent = labelText;
        wrapper.appendChild(label);
    }
    const select = document.createElement('select');
    select.name = name;
    options.forEach(({ value, text }) => {
        const opt = document.createElement('option');
        opt.value = value;
        opt.textContent = text;
        select.appendChild(opt);
    });
    wrapper.appendChild(select);
    return wrapper;
};

//Creates a radio input.
//Name and labelText are used for the content in the label as well as identifying the name of forms for submission. 
//Checked is a bool that's entered into a radio to show which default option is checked upon loading. 
export const createCheckboxInput = (name, labelText = '', checked = false) => {
    const wrapper = document.createElement('div');
    const input = document.createElement('input');
    Object.assign(input, {
        type: 'checkbox',
        name,
        checked
    });
    wrapper.appendChild(input);
    if (labelText) {
        const label = document.createElement('label');
        label.htmlFor = name;
        label.textContent = labelText;
        wrapper.appendChild(label);
    }
    return wrapper;
};



//Creates a radio input.
//Name and labelText are used for the content in the label as well as identifying the name of forms for submission. 
//The value is used for a default value to be entered. This makes sure that the form isn't submitted with nulls within it.
//Checked is a bool that's entered into a radio to show which default option is checked upon loading. 
export const createRadioInput = (name, value, labelText, checked) => {
    const wrapper = document.createElement('div');
    const input = document.createElement('input');
    Object.assign(input, {
        type: 'radio',
        name,
        value,
        checked
    });
    wrapper.appendChild(input);
    if (labelText) {
        const label = document.createElement('label');
        label.htmlFor = name;
        label.textContent = labelText;
        wrapper.appendChild(label);
    }
    return wrapper;
};

//Creates a data input.
//Name and labelText are used for the content in the label as well as identifying the name of forms for submission. 
//the value is used for a default value to be entered. This makes sure that the form isn't submitted with nulls within it.
export const createDateInput = (name, labelText = '', value = '') => {
    const wrapper = document.createElement('div');
    if (labelText) {
        const label = document.createElement('label');
        label.htmlFor = name;
        label.textContent = labelText;
        wrapper.appendChild(label);
    }
    const input = document.createElement('input');
    Object.assign(input, {
        type: 'date',
        name,
        value
    });
    wrapper.appendChild(input);
    return wrapper;
};


//Creates a file input.
//Name and labelText are used for the content in the label as well as identifying the name of forms for submission. 
//accept is to filter what kind of media-files can be shown in the file input dialog box. 
//(eg. You want an input that only shows "audio". So you input "audio/*" inside the accept parameter.) 
export const createFileInput = (name, labelText = '', accept = '') => {
    const wrapper = document.createElement('div');
    if (labelText) {
        const label = document.createElement('label');
        label.htmlFor = name;
        label.textContent = labelText;
        wrapper.appendChild(label);
    }
    const input = document.createElement('input');
    Object.assign(input, {
        type: 'file',
        name,
        accept
    });
    wrapper.appendChild(input);
    return wrapper;
};


//Takes an inputted wrapper with an input inside and sets that input's required field to true.
//throws error is wrapper is null. 
export const setRequiredTrue = (wrapper) => {
    const input = wrapper.querySelector('input');
    if (input) {
        input.required = true;
    } else {
        console.error('Input element not found within the provided wrapper.');
    }
    return wrapper;
};


//Take a form and replaces the default event listener with the inputted callback function. 
//This is so the default form submission can be changed to one than can be handled by JS for validation reasons. :)
export const addSubmitEventListener = (form, callback) => {
    if (form) {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            await callback(event);
        });
    }
};


// Adds and removes an "Validation Error" message upon verification. 
export const addInputValidationListener = (input, isValid, message) => {

    const errorElement = input.nextElementSibling; // Assumes error element is right after input
    if (isValid) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        errorElement.textContent = ''; // Clear error message
    } else {
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
        errorElement.textContent = message; // Show error message
    }
};




//takes a passed email value and tests it. returning the resulting bool.
export const validateEmail = (email) => /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email);

//takes the inputted password and inputted default length and tests the password against the return values. 
//Returning true if all conditions are met, otherwise false.
export const validatePasswordStrength = (password, minLength) => {

    if(!/[A-Z]/.test(password)){
        return [false, "Password needs an Uppercase Letter."];
    }
    
    if(!/[a-z]/.test(password)) {
        return [false, "Password needs an lowercase Letter."];
    }

    if(!/[0-9]/.test(password)) {
        return [false, "Password needs an number."];
    }
    
    if(!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
        return [false, "Password needs to have a special character."];
    }

    if(password.length < 8) {
        return [false, "Password needs to be longer than eight characters."];
    }

    return [true, "Password is valid."];
};

//Checks to see if the username is valid.
export function validateUsername(username) {
    // Check the length (between 3 and 20 characters)
    if (username.length < 3 || username.length > 20) {
        return [false, "Username must be between 3 and 20 characters long."];
    }

    // Check for allowed characters (letters, numbers, underscores, and dots)
    const allowedCharacters = /^[a-zA-Z0-9._]+$/;
    if (!allowedCharacters.test(username)) {
        return [false, "Username can only contain letters, numbers, underscores, and dots."];
    }

    // Ensure it doesn't start or end with an underscore or dot
    const invalidStartOrEnd = /^[_\.]|[_\.]$/;
    if (invalidStartOrEnd.test(username)) {
        return [false, "Username cannot have consecutive dots or underscores."];
    }

    // Ensure there are no consecutive dots or underscores
    const consecutiveDotsOrUnderscores = /[_.]{2,}/;
    if (consecutiveDotsOrUnderscores.test(username)) {
        return false;
    }

    return [true, "Username is valid."];  // All checks passed
}


//Checks if a required field is empty. 
//(In case of inputs that don't use the required field.)
export const validateRequiredField = value => value.trim() !== '';


//takes the inputted value and the inputted regex pattern and returns the bool value of the test. 
//This is for a more flexible regex function. 
export const validateWithRegex = (value, pattern) => new RegExp(pattern).test(value);


//Uses an inputted pattern to validate the date and then checks if the date is valid.
export const validateDateFormat = (date, pattern) => {
    const regex = new RegExp(pattern);
    if (!regex.test(date)) return false;

    // Destructure date components
    const [year, month, day] = date.split('-').map(Number);
    const dateObj = new Date(year, month - 1, day);

    return (
        dateObj.getFullYear() === year &&
        dateObj.getMonth() === month - 1 &&
        dateObj.getDate() === day
    );
};



//Submits the form via an async function to ensure things load correctly. 
export const submitFormAsync = async (form, url) => {
    try {
      // Collect form data and convert it to a JSON object
      const formDataObj = Object.fromEntries(new FormData(form).entries());
  
      // Send the form data to the server
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(formDataObj), // Send data in JSON format
      });
  
      // Check for HTTP response errors
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
  
      // Parse the response JSON
      const result = await response.json();
      handleResponse(result); // Call the response handler function with the result
    } catch (error) {
      console.error('Form submission failed:', error);
      handleResponse({ success: false, message: error.message });
    }
  };


//handles the response from the submission.
  export const handleResponse = ({ success, message }) => {
    const messageContainer = document.getElementById('responseMessage'); // Element to display messages
  
    messageContainer.textContent = success
      ? message || 'Form submitted successfully!'
      : message || 'An error occurred while submitting the form.';
  
    messageContainer.style.color = success ? 'green' : 'red'; // Green for success, red for errors
    messageContainer.style.display = 'block'; // Ensure the message is visible
  };


//Handles the form submission.
  export const handleFormSubmission = (event) => {
    event.preventDefault();
    const form = event.target;
    const url = '/submit'; // Change this to your actual server endpoint
  
    const messageContainer = document.getElementById('responseMessage');
    messageContainer.textContent = 'Submitting...';
    messageContainer.style.color = 'blue';
    messageContainer.style.display = 'block';
  
    submitFormAsync(form, url).then(() => {
      messageContainer.style.display = 'none';
    });
  };


//formats the phone number.
  export const formatPhoneNumber = (event) => {
    const input = event.target;
    let value = input.value.replace(/\D/g, ''); // Remove all non-digit characters
  
    if (value.length > 3 && value.length <= 6) {
      value = `(${value.slice(0, 3)}) ${value.slice(3)}`;
    } else if (value.length > 6) {
      value = `(${value.slice(0, 3)}) ${value.slice(3, 6)}-${value.slice(6, 10)}`;
    } else {
      value = `(${value.slice(0, 3)}) ${value.slice(3)}`;
    }
  
    input.value = value;
};


//Capitalizes the Name.
export const capitalizeName = (event) => {
    const input = event.target;
    const words = input.value.split(' ');
    const capitalizedWords = words.map(word => 
      word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
    );
    input.value = capitalizedWords.join(' ');
  };


//formats the inputted date.
  export const formatDate = (event) => {
    const input = event.target;
    let value = input.value.replace(/\D/g, ''); // Remove all non-digit characters
  
    if (value.length > 2 && value.length <= 4) {
      value = `${value.slice(0, 2)}/${value.slice(2)}`;
    } else if (value.length > 4) {
      value = `${value.slice(0, 2)}/${value.slice(2, 4)}/${value.slice(4, 8)}`;
    }
  
    input.value = value;
  };


//displays an error.
  export const displayError = (field, message) => {
    // Find or create the error message element
    let errorElement = field.nextElementSibling;
    if (!errorElement || !errorElement.classList.contains('error-message')) {
        errorElement = document.createElement('span');
        errorElement.className = 'error-message';
        field.parentNode.insertBefore(errorElement, field.nextSibling);
    }

    // Set the error message
    errorElement.textContent = message;
    errorElement.style.color = 'red'; // Display errors in red
};

//Clears an error. 
export const clearError = (field) => {
    const errorElement = field.nextElementSibling;
    if (errorElement && errorElement.classList.contains('error-message')) {
        errorElement.textContent = '';
        errorElement.style.display = 'none'; // Hide the error message
    }
};


//Sets the cookie.
export function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

//finds the cookie if available.
export function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}