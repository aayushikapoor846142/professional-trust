<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Badges</title>
    <style>
        .badge-container {
            width: 400px;
            background: #dadada;
            border-radius: 10px;
            /* box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); */
            padding: 20px;
            text-align: center;
            margin: 50px auto;
            position: relative;
        }

        .badge-image {
            width: 120px;
            height: 120px;
            margin-bottom: 15px;
        }

        .bronze {
            color: #cd7f32;
        }

        .silver {
            color: #c0c0c0;
        }

        .gold {
            color: #ffd700;
        }

        .platinum {
            color: #e5e4e2;
        }

        h4 {
            margin: 10px 0;
        }

        .points {
            font-weight: bold;
            color: #333;
        }
        .badge-user{
            float: left;
        }
        .badge-info{
            float: right;
        }
    </style>
</head>

<body>

    <div class="badge-container">
        <div class="badge-detail">
            <div class="badge-user">
                <h3>{{ $user->first_name }} {{ $user->last_name }}</h3>
            </div>
            <div class="badge-info">
                <img id="badgeImage" class="badge-image" src="{{ otherFileDirUrl($badge->badge_image,'m') }}"
                    alt="{{ $badge->badge_name }}">
                <h4 id="badgeName">{{ $badge->badge_name }}</h4>
            </div>
            <div style="clear:both"></div>
        </div>

    </div>


</body>

</html>
