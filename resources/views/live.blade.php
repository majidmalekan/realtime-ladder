<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Live Leaderboard</title>
    <style>
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            margin: 24px;
        }
        h1 { margin-bottom: 12px; }
        table { border-collapse: collapse; width: 480px; }
        th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; }
        th { background: #f5f5f5; }
        .score-up { font-weight: 600; color: #007bff; }
    </style>
</head>
<body>
<h1>Live Leaderboard</h1>
<p>Showing top <span id="limit-display"></span> players — refreshes every 1s</p>
<table>
    <thead>
    <tr><th>#</th><th>Player</th><th>Score</th></tr>
    </thead>
    <tbody id="rows"></tbody>
</table>

<script>
    const params = new URLSearchParams(window.location.search);
    const limit = parseInt(params.get('limit')) || 10;
    document.getElementById('limit-display').textContent = limit;

    let lastScores = {};

    async function fetchTop() {
        try {
            const res = await fetch(`/api/v1/leaderboard?limit=${limit}`, { cache: 'no-cache' });
            const data = await res.json();
            const tbody = document.getElementById('rows');
            tbody.innerHTML = '';

            data.forEach((row, i) => {
                const prev = lastScores[row.player_id] ?? row.score;
                const changed = row.score !== prev;
                const tr = document.createElement('tr');
                tr.innerHTML = `
          <td>${i + 1}</td>
          <td>${row.name ?? ('#' + row.player_id)}</td>
          <td class="${changed ? 'score-up' : ''}">${row.score}</td>
        `;
                tbody.appendChild(tr);
                lastScores[row.player_id] = row.score;
            });
        } catch (e) {
            console.error('Error fetching leaderboard:', e);
        }
    }

    fetchTop();
    setInterval(fetchTop, 1000);
</script>
</body>
</html>
