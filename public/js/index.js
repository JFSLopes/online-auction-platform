function sanitizeInput(input) {

  input = String(input);

  const map = new Map([
      ['&', '&amp;'],
      ['<', '&lt;'],
      ['>', '&gt;'],
      ['"', '&quot;'],
      ["'", '&#x27;'],
      ['/', '&#x2F;']
  ]);

  return input.replace(/[&<>"'/]/g, match => map.get(match));
}

function checkEndendAuctions() {
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  fetch('checkEndendAuctions', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({})
  })
  .then(response => {
      if (!response.ok) {
          throw new Error('Network response was not ok');
      }
      return response.json();
  })
  .then(data => {
  })
  .catch(error => {
      console.error('Error:', error);
  });
}

// Call this function every 5 minutes
setInterval(() => checkEndendAuctions(), 300000); 
