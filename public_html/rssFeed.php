<?php
include 'database_connection.php'; // Include your MySQL DB connection

header('Content-Type: application/json');

// Function to retrieve liked artists for a user and filter the RSS feed
function getUserRSSFeed($user_id, $pdo) {
    try {
        // Retrieve liked artists for the user
        $stmt = $pdo->prepare("
            SELECT a.name 
            FROM artists a
            JOIN user_likes_dislikes uld ON a.artist_id = uld.song_id
            WHERE uld.user_id = ? AND uld.liked = TRUE
        ");
        $stmt->execute([$user_id]);
        $liked_artists = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($liked_artists)) {
            return ["status" => "error", "message" => "No liked artists found for this user."];
        }

        // Fetch and filter RSS feed based on liked artists
        $url = "https://musicnewsrss.com/artist/";
        $rss_feed = fetchRSS($url);

        $filtered_feed = array_filter($rss_feed, function($item) use ($liked_artists) {
            foreach ($liked_artists as $artist) {
                if (stripos($item['title'], $artist) !== false) {
                    return true;
                }
            }
            return false;
        });

        return ["status" => "success", "data" => array_values($filtered_feed)];
    } catch (Exception $e) {
        return ["status" => "error", "message" => "Failed to retrieve RSS feed: " . $e->getMessage()];
    }
}

// Function to fetch RSS feed from an external URL
function fetchRSS($url) {
    try {
        $rss = simplexml_load_file($url);
        $items = [];
        foreach ($rss->channel->item as $item) {
            $items[] = [
                'title' => (string) $item->title,
                'link' => (string) $item->link,
                'description' => (string) $item->description
            ];
        }
        return $items;
    } catch (Exception $e) {
        return [];
    }
}

// Retrieve user ID from query parameter
$user_id = $_GET['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(["status" => "error", "message" => "Please provide a user_id."]);
    exit;
}

// Generate and output the filtered RSS feed
$response = getUserRSSFeed($user_id, $pdo);
echo json_encode($response);
?>

