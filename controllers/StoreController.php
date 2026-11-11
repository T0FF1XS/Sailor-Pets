<?php

class StoreController
{
    public function home($track = null, $trackError = '')
    {
        $products = [];
        foreach (Product::forStore() as $r) {
            $products[] = [
                'id' => (int) $r['product_id'],
                'name' => $r['product_name'],
                'price' => (float) $r['retail_price'],
                'wholesale' => (float) $r['wholesale_price'],
                'stock' => (int) $r['stock'],
                'tag' => $r['tag'] ?? '',
                'img' => photo_url('products/' . $r['sku']) ?: photo_url('categories/' . slug($r['category_name'])),
                'pets' => $r['pet_type'],
                'cat' => $r['category_name'],
            ];
        }

        $icons = [
            'Treats' => 'bag-heart', 'Dog Food' => 'basket', 'Cat Food' => 'basket2', 'Bird Food' => 'flower1',
            'Litter Sand' => 'bucket', 'Cat Litter Box & Accessories' => 'box-seam', 'Dog & Cat Shampoo' => 'droplet',
            'Anti Tick & Flea' => 'bug', 'Pet Milk & Bird Formula' => 'cup-straw', 'Pet Cologne' => 'stars',
            'Ear/Eye Drops' => 'eyedropper', 'Wound Medicine' => 'bandaid', 'e-Collar' => 'life-preserver',
            'Muzzle/Busal' => 'shield-shaded', 'Nursing Bottle' => 'emoji-smile', 'Bowls & Bottles' => 'cup-hot',
            'Grooming' => 'scissors', 'Toys' => 'puzzle', 'Bed & Carrier' => 'house-heart',
            'Collars/Leashes/Harness' => 'link-45deg', 'Pee Pad & Diaper' => 'layers', 'Other Products' => 'gift',
            'Cages/Crates' => 'grid-3x3', 'Plastic Matting' => 'grid', 'Vitamins & Meds' => 'capsule',
            'Deworming' => 'virus', 'Pet Soap' => 'moisture', 'Wet Food in Can' => 'box2-heart',
        ];

        $catalogs = [];
        foreach (Category::allById() as $c) {
            $catalogs[] = [
                $c['category_name'],
                $icons[$c['category_name']] ?? 'tag',
                photo_url('categories/' . slug($c['category_name'])),
            ];
        }

        render('store/index', [
            'pageTitle' => 'Sailor Pets',
            'products' => $products,
            'catalogs' => $catalogs,
            'track' => $track,
            'trackError' => $trackError,
        ], 'store');
    }

    public function send()
    {
        $name = trim($_POST['customer_name']);
        $phone = trim($_POST['customer_phone']);
        $email = trim($_POST['customer_email'] ?? '');
        $address = trim($_POST['customer_address'] ?? '');
        $message = substr(trim($_POST['message'] ?? ''), 0, 255);
        $type = ($_POST['inquiry_type'] == 'Wholesale') ? 'Wholesale' : 'Retail';
        $column = ($type == 'Wholesale') ? 'wholesale_price' : 'retail_price';

        $items = [];
        foreach ($_POST['qty'] ?? [] as $productId => $quantity) {
            if ((int) $quantity > 0) {
                $items[(int) $productId] = (int) $quantity;
            }
        }

        $error = '';
        if ($name == '' || $phone == '') {
            $error = 'Please enter your name and phone number.';
        } elseif ($email != '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (!$items) {
            $error = 'Select at least one product.';
        } else {
            foreach ($items as $productId => $quantity) {
                $product = Product::priceFor($productId, $column);
                if (!$product) {
                    $error = 'One of the products is no longer available.';
                    break;
                }
                if ($quantity > $product['stock']) {
                    $error = 'Only ' . $product['stock'] . ' left for ' . $product['product_name'] . '.';
                    break;
                }
            }
        }

        if ($error == '') {
            $inquiryId = Inquiry::create($name, $phone, $email, $address, $type, $message, $column, $items);

            if ($inquiryId) {
                redirect('inquiry_sent', ['id' => $inquiryId]);
            }

            $error = 'Could not send the inquiry. Please try again.';
        }

        flash($error);
        redirect('home');
    }

    public function sent()
    {
        $id = (int) ($_GET['id'] ?? 0);

        if (!$id) {
            redirect('home');
        }

        render('store/sent', ['pageTitle' => 'Inquiry Sent', 'sentId' => $id], 'store');
    }

    public function track()
    {
        $track = null;
        $trackError = '';

        if (isset($_POST['track_id'])) {
            $track = Inquiry::findForTrack((int) $_POST['track_id'], trim($_POST['track_phone']));
            if (!$track) {
                $trackError = 'No inquiry found. Check the number and the phone you used.';
            }
        }

        $this->home($track, $trackError);
    }
}
