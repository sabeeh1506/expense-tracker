document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('expense-form');
  const transactionList = document.getElementById('transaction-list');

  form.addEventListener('submit', function (e) {
      e.preventDefault();

      const description = document.getElementById('description').value;
      const amount = document.getElementById('amount').value;
      const date = document.getElementById('date').value;
      const type = document.getElementById('type').value;

      fetch('process.php', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: new URLSearchParams({
              action: 'add',
              description: description,
              amount: amount,
              date: date,
              type: type
          })
      })
      .then(response => response.json())
      .then(data => {
          if (data.success) {
              const transaction = data.transaction;
              const newItem = document.createElement('li');
              newItem.setAttribute('data-id', transaction.id);
              newItem.innerHTML = `
                  ${transaction.description} - 
                  $${transaction.amount.toFixed(2)} - 
                  ${new Date(transaction.date).toLocaleDateString()} - 
                  ${transaction.type.charAt(0).toUpperCase() + transaction.type.slice(1)}
                  <button class="delete-btn">Delete</button>
              `;
              transactionList.appendChild(newItem);
              updateTotals();
              form.reset();
          } else {
              alert('Failed to add transaction');
          }
      });
  });

  transactionList.addEventListener('click', function (e) {
      if (e.target.classList.contains('delete-btn')) {
          const item = e.target.parentElement;
          const id = item.getAttribute('data-id');

          fetch(`process.php?action=delete&id=${id}`, {
              method: 'GET'
          })
          .then(response => response.json())
          .then(data => {
              if (data.success) {
                  item.remove();
                  updateTotals();
              } else {
                  alert('Failed to delete transaction');
              }
          });
      }
  });

  function updateTotals() {
      fetch('process.php?action=get_totals')
      .then(response => response.json())
      .then(data => {
          document.getElementById('income-amount').textContent = data.incomeTotal.toFixed(2);
          document.getElementById('expense-amount').textContent = data.expenseTotal.toFixed(2);
      });
  }
});
