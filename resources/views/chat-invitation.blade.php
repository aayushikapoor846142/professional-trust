<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Invitation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="vh-100 d-flex justify-content-center align-items-center">
        <div class="text-center">
            <div class="mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="text-warning" width="75" height="75"
                    fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm4.736 5.09a.5.5 0 0 1 .592.805l-5 6a.5.5 0 0 1-.761 0l-3-4a.5.5 0 0 1 .743-.667L8 10.293l4.255-5.203a.5.5 0 0 1 .481-.136z" />
                </svg>
            </div>
            <h3 class="mb-4">To Accept Chat Invitation</h3>
            <p class="mb-4">
                You need to create an account to accept this chat invitation.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ mainTrustvisoryUrl() }}/register/professional" class="CdsTYButton-btn-primary">Create Professional Account</a>
                <a href="{{ clientTrustvisoryUrl() }}/register" class="CdsTYButton-btn-primary">Create Client Account</a>
      
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<!--  -->