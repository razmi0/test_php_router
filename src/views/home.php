<?php session_start(); ?>
<!DOCTYPE html>
<html data-theme="dark">

<head>
    <title>API Product Interface</title>
    <script src="/APIFetch.js" type="module"></script>
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.colors.min.css">
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.lime.min.css">

    <link rel="stylesheet" href="index.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="https://img.icons8.com/?size=512w&id=13910&format=png">
</head>

<body style="padding: 2rem;">

    <?php include("header.php"); ?>

    <!--     -->
    <!-- NAV -->
    <!--     -->


    <main class="container-fluid">
        <?php if (!isset($_SESSION['id'])) : ?>
            <article>
                <h2>Veuillez vous authentifiez pour profiter de l'API</h2>
                <p>Connectez-vous : <a href="/login">Login</a></p>
                <p>Créez un compte : <a href="/signup">Signup</a></p>
            </article>
        <?php else : ?>


            <!--        -->
            <!-- CREATE -->
            <!--        -->

            <article>
                <section data-endpoint="create">
                    <header style="margin-bottom : 1rem;">
                        <h2 style="margin-bottom: 0;">Create a new product</h2>
                    </header>
                    <form>
                        <fieldset class="grid">
                            <div>
                                <label for="name">Name:</label>
                                <input type="text" id="name" name="name" placeholder="Enter name..." required>
                            </div>
                            <div>
                                <label for="prix">Price:</label>
                                <input type="text" id="prix" name="prix" placeholder="Enter price..." required>
                            </div>
                        </fieldset>
                        <fieldset>
                            <label for="description">Description:</label>
                            <input type="text" id="description" name="description" placeholder="Enter description..." required>
                        </fieldset>
                        <button type="button" disabled>Create this one</button>
                    </form>
                </section>
                <section>

                    <div class="grid">
                        <div id='error'></div>
                        <pre id='error_data'>
                        </pre>
                        <div id='message'></div>
                    </div>
                </section>
            </article>

            <!--        -->
            <!-- READ   -->
            <!--        -->

            <article>
                <section data-endpoint="read">
                    <h2>List all products</h2>
                    <button type="button">List all products</button>
                </section>
                <section class="overflow-auto">

                </section>
            </article>

            <!--        -->
            <!-- READ ONE -->
            <!--        -->

            <article>
                <section data-endpoint="read-one">
                    <h2>Find by id</h2>
                    <form>
                        <fieldset role="group">
                            <input type="text" id="id" name="id" placeholder="Enter an id..." required>
                            <label style="display: none;" for="id">Identifier :</label>
                            <button disabled type="button">Find this one</button>
                        </fieldset>
                    </form>
                </section>
                <section>

                </section>
            </article>

            <!--        -->
            <!-- UPDATE -->
            <!--        -->

            <article>
                <section data-endpoint="update">
                    <h2>Update a product</h2>
                    <small>Choose an id :</small>
                    <div data-ids class="overflow-auto">

                    </div>
                    <form>
                        <fieldset class="grid">
                            <div>
                                <label for="name">Name:</label>
                                <input type="text" id="name" name="name" placeholder="Enter name..." required>
                            </div>
                            <div>
                                <label for="prix">Price:</label>
                                <input type="text" id="prix" name="prix" placeholder="Enter price..." required>
                            </div>
                        </fieldset>
                        <label for="stock">Description:</label>
                        <input type="text" id="description" name="description" placeholder="Enter description..." required>
                    </form>
                    <button id="submitUpdate" type="button">Update this one</button>
                </section>
                <section data-output="update">
                    <div id='update_error'></div>
                    <div id='update_message'></div>
                </section>
            </article>

            <!--        -->
            <!-- DELETE -->
            <!--        -->

            <article>
                <section data-endpoint="delete">
                    <h2>Delete a product</h2>
                    <form>
                        <fieldset role="group">
                            <input type="text" id="id" name="id" placeholder="Enter an id ..." required>
                            <label style="display: none;" for="id">Identifier :</label>
                            <button disabled type="button">Delete this one</button>
                        </fieldset>
                    </form>
                </section>
                <section>
                    <div class="grid">
                        <div id='error'></div>
                        <pre id='error_data'>
                        </pre>
                        <div id='message'></div>
                    </div>

                </section>
            </article>
        <?php endif; ?>
    </main>
</body>

</html>