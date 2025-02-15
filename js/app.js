// js/app.js
document.addEventListener('DOMContentLoaded', function() {
    // Handle sale form submission
    const saleForm = document.getElementById('saleForm');
    if (saleForm) {
      saleForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission
  
        const formData = new FormData(saleForm);
        fetch(saleForm.action, {
          method: 'POST',
          body: formData
        })
        .then(response => response.text())
        .then(data => {
          document.getElementById('modalMessage').innerText = data;
          document.getElementById('modal').style.display = 'block';
        })
        .catch(error => console.error('Error:', error));
      });
    }

    const closeModal = document.getElementById('closeModal');
    if (closeModal) {
      closeModal.addEventListener('click', function() {
        document.getElementById('modal').style.display = 'none';
      });
    }
    window.addEventListener('click', function(e) {
      const modal = document.getElementById('modal');
      if (e.target == modal) {
        modal.style.display = 'none';
      }
    });
  });
  