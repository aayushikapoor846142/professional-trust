// ticket-user-socket.js
// This file mirrors the user socket logic in chatapp.js, but for ticket-user websocket actions.

$(document).ready(function () {
    console.log('Document ready - initializing ticket-user socket');
    
    if (typeof currentUserId === 'undefined') {
        console.error('currentUserId is undefined');
        return;
    }
    
    if (!window.Echo) {
        console.error('window.Echo is not defined');
        return;
    }
    
    console.log('Initializing ticket-user socket for user:', currentUserId);
    
    // Leave previous ticket-user channel if needed
    window.Echo.leave(`ticket-user.` + currentUserId);
    
    // Subscribe to the private channel
    
    // Listen for TicketSystemSocket events
    window.Echo.private(`ticket-user.${currentUserId}`).listen(".TicketSystemSocket", (e) => {
          const response = e.data;
         
        if (response && response.action === "ticket_reply" && response.reply_html) {
            console.log('Processing ticket_reply action');
            console.log('Reply HTML received:', response.reply_html);
            
            const container = document.getElementById('replies-container');
            console.log('Container found:', container);
            
            if (container) {
                // Append the new reply HTML
                console.log('Reply HTML appended to container successfully');
                
                // Scroll to bottom to show new reply
                container.scrollTop = container.scrollHeight;
                
                // Also try jQuery append as fallback
                $(container).append(response.reply_html);
                
            } else {
                console.error('replies-container not found');
                // Try alternative selectors
                const altContainer = document.querySelector('.CdsTicket-conversation #replies-container') || 
                                   document.querySelector('#replies-container') ||
                                   document.querySelector('.replies-container');
                if (altContainer) {
                    altContainer.insertAdjacentHTML('beforeend', response.reply_html);
                    console.log('Reply appended to alternative container');
                } else {
                    console.error('No container found for replies');
                }
            }
        } else {
            console.log('Action not ticket_reply or no reply_html:', response);
        }
    });
    
    console.log('Ticket-user socket initialization complete');
}); 