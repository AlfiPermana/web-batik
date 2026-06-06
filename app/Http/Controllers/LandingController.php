<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Workshop;
use App\Models\Gallery;
use App\Mail\ContactFormMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LandingController extends Controller
{
    public function home()
    {
        $products = Product::with(['sizes', 'images'])->latest()->take(4)->get();
        $workshops = Workshop::latest()->take(3)->get();

        return view('landing.home', compact('products', 'workshops'));
    }

    public function about()
    {
        return view('landing.about');
    }

    public function shop()
    {
        $products = Product::with(['sizes', 'images'])->latest()->paginate(12);

        return view('landing.shop', compact('products'));
    }

    public function productDetail($id)
    {
        $product = Product::with(['sizes', 'images'])->findOrFail($id);

        return view('landing.product-detail', compact('product'));
    }

    public function workshop()
    {
        $workshops = Workshop::latest()->take(3)->get();
        $galleries = Gallery::latest()->take(12)->get();

        $imageFiles = array_map(
            static fn (int $i) => $i . '.jpeg',
            range(1, 12)
        );

        // Ganti gambar ke-3 (3.jpeg) dengan gambar lain
        $imageFiles[2] = '18.jpeg';

        return view('landing.workshop', compact('workshops', 'galleries', 'imageFiles'));
    }

    public function contact()
    {
        return view('landing.contact');
    }

    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'nullable|string',
        ]);

        try {
            // Send email to zetgaming61@gmail.com
            Mail::to('zetgaming61@gmail.com')->send(new ContactFormMail($validated));

            return redirect()->route('landing.contact')->with('success', 'Thank you for contacting us! We will get back to you soon.');
        } catch (\Exception $e) {
            return redirect()->route('landing.contact')->with('error', 'Sorry, there was an error sending your message. Please try again later.');
        }
    }
}
