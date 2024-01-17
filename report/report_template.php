<table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Hours</th>
                <!-- Add more table headers as needed -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['total_hours']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>