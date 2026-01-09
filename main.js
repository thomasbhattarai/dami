// Custom Dialog Box Function
function showDialog(message, callback = null, showCancel = false) {
    const dialogBox = document.createElement('div');
    dialogBox.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        animation: fadeInBackdrop 0.3s ease;
    `;

    const dialogContent = document.createElement('div');
    dialogContent.style.cssText = `
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        padding: 40px;
        border-radius: 16px;
        max-width: 450px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3), 0 0 1px rgba(0, 0, 0, 0.1);
        text-align: center;
        border: 1px solid rgba(255, 255, 255, 0.8);
        animation: slideInDialog 0.3s ease;
    `;

    const messageElement = document.createElement('p');
    messageElement.textContent = message;
    messageElement.style.cssText = `
        margin: 0 0 30px 0;
        font-size: 18px;
        color: #2c3e50;
        font-weight: 500;
        line-height: 1.6;
    `;

    // Button container
    const buttonContainer = document.createElement('div');
    buttonContainer.style.cssText = `
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
    `;

    // OK Button
    const okButton = document.createElement('button');
    okButton.textContent = 'OK';
    okButton.style.cssText = `
        background: linear-gradient(135deg, #ff7b00 0%, #ff5500 100%);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 15px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(255, 123, 0, 0.3);
        letter-spacing: 0.5px;
    `;
    
    okButton.onmouseover = function() {
        this.style.transform = 'translateY(-2px)';
        this.style.boxShadow = '0 6px 20px rgba(255, 123, 0, 0.4)';
    };
    
    okButton.onmouseout = function() {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = '0 4px 15px rgba(255, 123, 0, 0.3)';
    };

    okButton.onclick = function() {
        dialogBox.remove();
        if (callback) callback();
    };

    buttonContainer.appendChild(okButton);

    // Cancel Button (if needed)
    if (showCancel) {
        const cancelButton = document.createElement('button');
        cancelButton.textContent = 'Cancel';
        cancelButton.style.cssText = `
            background: linear-gradient(135deg, #e0e0e0 0%, #d0d0d0 100%);
            color: #333;
            border: 1px solid #c0c0c0;
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            letter-spacing: 0.5px;
        `;
        
        cancelButton.onmouseover = function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
            this.style.background = 'linear-gradient(135deg, #f0f0f0 0%, #e0e0e0 100%)';
        };
        
        cancelButton.onmouseout = function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.1)';
            this.style.background = 'linear-gradient(135deg, #e0e0e0 0%, #d0d0d0 100%)';
        };

        cancelButton.onclick = function() {
            dialogBox.remove();
        };

        buttonContainer.appendChild(cancelButton);
    }

    dialogContent.appendChild(messageElement);
    dialogContent.appendChild(buttonContainer);
    dialogBox.appendChild(dialogContent);
    document.body.appendChild(dialogBox);

    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInBackdrop {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideInDialog {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    document.head.appendChild(style);
}

const cleaveCC = new Cleave("#cardNumber", {
    creditCard: true,
    delimiter: "-",
    onCreditCardTypeChanged: function (type) {
      const cardBrand = document.getElementById("cardBrand"),
        visa = "fab fa-cc-visa",
        mastercard = "fab fa-cc-mastercard",
        amex = "fab fa-cc-amex",
        diners = "fab fa-cc-diners-club",
        jcb = "fab fa-cc-jcb",
        discover = "fab fa-cc-discover";
  
      switch (type) {
        case "visa":
          cardBrand.setAttribute("class", visa);
          break;
        case "mastercard":
          cardBrand.setAttribute("class", mastercard);
          break;
        case "amex":
          cardBrand.setAttribute("class", amex);
          break;
        case "diners":
          cardBrand.setAttribute("class", diners);
          break;
        case "jcb":
          cardBrand.setAttribute("class", jcb);
          break;
        case "discover":
          cardBrand.setAttribute("class", discover);
          break;
        default:
          cardBrand.setAttribute("class", "");
          break;
      }
    },
  });
  
  const cleaveDate = new Cleave("#cardExpiry", {
    date: true,
    datePattern: ["m", "y"],
  });
  
  const cleaveCCV = new Cleave("#cardCcv", {
    blocks: [3],
  });