<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Library</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'nav.php'; ?> 

    <h1>My Library</h1>

    <!-- Add to Playlist Form -->
    <h2>Add Song to Playlist</h2>
    <form id="addToPlaylistForm">
        <input type="text" id="songId" placeholder="Enter song ID" required>
        <input type="text" id="playlistName" placeholder="Enter playlist name" required>
        <button type="submit">Add to Playlist</button>
    </form>

    <!-- Like/Dislike Songs -->
    <h2>Like/Dislike a Song</h2>
    <form id="likeDislikeForm">
        <input type="text" id="likeDislikeSongId" placeholder="Enter song ID" required>
        <button type="button" onclick="likeSong()">Like</button>
        <button type="button" onclick="dislikeSong()">Dislike</button>
    </form>

    <!-- View Playlists -->
    <h2>Your Playlists</h2>
    <div id="playlistsInfo" class="results"></div>

    <script>
        // Add Song to Playlist Handler
        document.getElementById('addToPlaylistForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const songId = document.getElementById('songId').value;
            const playlistName = document.getElementById('playlistName').value;

            fetch(`scripts/add_to_playlist.php?song_id=${encodeURIComponent(songId)}&playlist_name=${encodeURIComponent(playlistName)}`)
                .then(response => response.json())
                .then(data => {
                    alert(data.message || 'Song added to playlist!');
                })
                .catch(error => console.error('Error:', error));
        });

        // Like a Song
        function likeSong() {
            const songId = document.getElementById('likeDislikeSongId').value;
            fetch(`scripts/like_dislike_song.php?song_id=${encodeURIComponent(songId)}&action=like`)
                .then(response => response.json())
                .then(data => alert(data.message || 'Song liked!'))
                .catch(error => console.error('Error:', error));
        }

        // Dislike a Song
        function dislikeSong() {
            const songId = document.getElementById('likeDislikeSongId').value;
            fetch(`scripts/like_dislike_song.php?song_id=${encodeURIComponent(songId)}&action=dislike`)
                .then(response => response.json())
                .then(data => alert(data.message || 'Song disliked!'))
                .catch(error => console.error('Error:', error));
        }

        // Fetch and Display Playlists
        function loadPlaylists() {
            fetch('scripts/view_playlists.php')
                .then(response => response.json())
                .then(data => {
                    const playlistsInfo = document.getElementById('playlistsInfo');
                    playlistsInfo.innerHTML = '';
                    if (data.error) {
                        playlistsInfo.innerHTML = `<p>${data.error}</p>`;
                    } else {
                        data.forEach(playlist => {
                            playlistsInfo.innerHTML += `
                                <div class="playlist">
                                    <p><strong>Playlist Name:</strong> ${playlist.name}</p>
                                    <p><strong>Songs:</strong> ${playlist.songs.join(', ')}</p>
                                </div>
                                <hr>
                            `;
                        });
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Load playlists on page load
        document.addEventListener('DOMContentLoaded', loadPlaylists);
    </script>
</body>
</html>

