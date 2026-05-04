<?php include 'header.php'; ?>

<main class="container">
    <div class="form-container">
        <h2 style="text-align:center;">Sell an Item</h2>
        <hr>
        <!-- The 'action' attribute tells the browser where to send the data -->
        <form action="process_sell.php" method="POST" enctype="multipart/form-data">
            <div class="file-upload-area">
                <b>Drag and drop your image here</b><br><br>
                <input type="file" name="item_image" accept="image/*" required>
            </div>

            <div class="form-group">
                <label>Item Name:</label>
                <input type="text" name="item_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Brand:</label>
                <input type="text" name="brand" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Price (R):</label>
                <input type="number" step="0.01" name="price" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Size:</label>
                <input type="text" name="size" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Condition:</label>
                <select name="condition" class="form-control" required>
                    <option value="New">New</option>
                    <option value="Like New">Like New</option>
                    <option value="Good">Good</option>
                </select>
            </div>

                                <div class="form-group">
                        <label>Category:</label>
                        <select name="category" class="form-control" required>
                            <option value="">-- Select One --</option>
                            <option value="tops">Tops</option>
                            <option value="bottoms">Bottoms</option>
                            <option value="outerwear">Outerwear</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Description:</label>
                        <textarea name="description" rows="5" class="form-control" required></textarea>
</div>
            <button type="submit" class="btn btn-full" style="background-color: green; border-color: darkgreen;">Submit Item</button>
        </form>
    </div>
</main>

<?php include 'footer.php'; ?>