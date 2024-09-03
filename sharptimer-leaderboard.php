function leaderboard_shortcode() {
    ob_start(); 

    echo '<style>
            @import url("https://fonts.googleapis.com/css2?family=HighSpeed:wght@400;700&display=swap");

            table.player-stats-table {
                width: 100%;
                border-collapse: collapse;
                background-color: #2E2E2E;
                color: #E0E0E0;
                border-radius: 10px;
                overflow: hidden;
            }
            table.player-stats-table th, table.player-stats-table td {
                padding: 10px;
                text-align: center;
            }
            table.player-stats-table th {
                background-color: #333;
            }
            table.player-stats-table tr:hover {
                background-color: #555;
            }
            .global-points-cell {
                background-size: 75px 35px;
                background-repeat: no-repeat;
                background-position: center; 
                height: 49px;
                width: 120px;
                font-family: "HighSpeed", sans-serif; 
                font-weight: bold; 
                font-style: italic; 
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center; 
            }
            .global-points-heading {
                text-align: left;
                padding-left: 20px;
            }
        </style>';

    // DB configuration
    $host = '127.0.0.1';
    $username = 'CHANGE_ME';
    $password = 'CHANGE_ME';
    $dbname = 'CHANGE_ME';
    $port = 3306;

    $conn = new mysqli($host, $username, $password, $dbname, $port);

    if ($conn->connect_error) {
        return "No connection: " . $conn->connect_error;
    }

    $sql = "
        SELECT pr.PlayerName, pr.SteamID, ps.GlobalPoints
        FROM PlayerRecords pr
        LEFT JOIN PlayerStats ps ON pr.PlayerName = ps.PlayerName
        GROUP BY pr.PlayerName
        ORDER BY ps.GlobalPoints DESC
        LIMIT 10
    ";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<table class="player-stats-table">
                <tr>
                    <th>Position</th>
                    <th>Player Name</th>
                    <th class="global-points-heading" style="text-align: left">Global Points</th>
                </tr>';
        
        $position = 1;

        while ($row = $result->fetch_assoc()) {
            $playerName = htmlspecialchars($row['PlayerName']);
            $playerPoints = intval($row['GlobalPoints']);
            $steamID = htmlspecialchars($row['SteamID']);
            
            if ($playerPoints > 0) {
                if ($playerPoints < 5000) {
                    $imageUrl = "https://cs2surf.pro/surf/images/ratings/rating.common.png";
                    $textColor = "#b1c3d9";
                    $fontSize = "22px"; 
                } elseif ($playerPoints < 10000) {
                    $imageUrl = "https://cs2surf.pro/surf/images/ratings/rating.uncommon.png";
                    $textColor = "#5e98d7";
                    $fontSize = "22px";
                } elseif ($playerPoints < 15000) {
                    $imageUrl = "https://cs2surf.pro/surf/images/ratings/rating.rare.png";
                    $textColor = "#4b69ff"; 
                    $fontSize = "19px";
                } elseif ($playerPoints < 20000) {
                    $imageUrl = "https://cs2surf.pro/surf/images/ratings/rating.mythical.png";
                    $textColor = "#8846ff"; 
                    $fontSize = "18px";
                } elseif ($playerPoints < 25000) {
                    $imageUrl = "https://cs2surf.pro/surf/images/ratings/rating.legendary.png";
                    $textColor = "#d22ce6"; 
                    $fontSize = "18px"; 
                } elseif ($playerPoints < 30000) {
                    $imageUrl = "https://cs2surf.pro/surf/images/ratings/rating.ancient.png";
                    $textColor = "#eb4b4b"; 
                    $fontSize = "18px";
                } else {
                    $imageUrl = "https://cs2surf.pro/surf/images/ratings/rating.unusual.png";
                    $textColor = "#fed701";
                    $fontSize = "18px"; 
                }
            }

            $playerLink = 'https://steamcommunity.com/profiles/' . urlencode($steamID);
            
            echo '<tr>
                    <td>' . $position . '</td>
                    <td><a href="' . esc_url($playerLink) . '" target="_blank">' . $playerName . '</a></td>
                    <td class="global-points-cell" style="background-image: url(' . $imageUrl . '); color: ' . $textColor . '; font-size: ' . $fontSize . ';">
                        ' . $playerPoints . '
                    </td>
                  </tr>';
            
            $position++; 
        }
        
        echo '</table>';
    } else {
        echo "No Data.";
    }

    $conn->close();

    return ob_get_clean(); 
}

function register_shortcodes(){
    add_shortcode('player_stats', 'leaderboard_shortcode');
}
add_action('init', 'register_shortcodes');
