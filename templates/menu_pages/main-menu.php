<div class="wrap">
    <h1 class="wp-heading">WP Social Login</h1>

    <?php if ( ! empty( $data['form_fields'] ) ) : ?>
    <form class="form" method="post" novalidate="novalidate">
        <table class="form-table">
            <tbody>
                <?php foreach ( $data['form_fields'] as $field_name => $field ) : ?>
                <tr>
                    <th scope="row">
                        <label for="<?php echo $field_name ?>"><?php echo $field['label'] ?></label>
                    </th>

                    <td scope="row">
                        <input 
                            type="text" 
                            id="<?php echo $field_name ?>" 
                            class="regular-text"
                            name="<?php echo $field_name ?>" 
                            value="<?php echo $field['value'] ?>"
                        >
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" class="button button-primary" value="Save Changes">
        </p>
    </form>
    <?php endif; ?>
</div>