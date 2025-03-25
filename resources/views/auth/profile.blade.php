<x-authenticated-layout class="Profile">
    <x-slot name="head">
        <title>Edit Profile</title>
    </x-slot>

    <section class="UserProfile">      
        <div class="custom_form">
            <div class="header">
                <p>
                    <a href="{{ route('dashboard') }}">
                        <span class="fas fa-arrow-left"></span>
                    </a>
                </p>
                <p class="title">Edit Profile</p>
            </div>

            <form action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('patch')
                
                <div class="image_input">
                    <div class="image" onclick="document.getElementById('image').click()">
                        @if($user->image)
                            <img id="previewImage" src="{{ asset('storage/' . $user->image) }}" alt="User" width="100" height="100">
                        @else
                            <img id="previewImage" src="{{ asset('assets/images/default_profile.jpg') }}" alt="Default Profile Image" width="100" height="100">
                        @endif
                    </div>
                
                    <input type="file" name="image" id="image" accept="image/*" onchange="previewSelectedImage(event)" style="display: none;">
                    <x-input-error field="image" />
                </div>

                <div class="input_group">
                    <div class="inputs">
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name" id="first_name" placeholder="First Name"
                            value="{{ old('first_name', $user->first_name) }}">
                        <x-input-error field="first_name" />
                    </div>
    
                    <div class="inputs">
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" id="last_name" placeholder="Last Name"
                            value="{{ old('last_name', $user->last_name) }}">
                        <x-input-error field="last_name" />
                    </div>
                </div>

                <div class="input_group_3">
                    <div class="inputs">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email" placeholder="Email Address"
                            value="{{ old('email', $user->email) }}">
                        <x-input-error field="email" />
                    </div>
    
                    <div class="inputs">
                        <label for="phone_number">Phone Number</label>
                        <input type="text" name="phone_number" id="phone_number" placeholder="Phone Number"
                            value="{{ old('phone_number', $user->phone_number) }}">
                        <x-input-error field="phone_number" />
                    </div>

                    <div class="inputs">
                        <label for="phone_other">Other Phone Number</label>
                        <input type="text" name="phone_other" id="phone_other" placeholder="Other Phone Number"
                            value="{{ old('phone_other', $user->phone_other) }}">
                        <x-input-error field="phone_other" />
                    </div>
                </div>

                <button type="submit">Update</button>
            </form>
        </div>

        <div class="custom_form">
            <div class="header">
                <p class="title">Update Password</p>
            </div>

            <form action="{{ route('password.update') }}" method="post">
                @csrf
                @method('put')

                <div class="inputs">
                    <label for="update_password_current_password">Current Password</label>
                    <input type="password" name="current_password" id="update_password_current_password"
                        placeholder="Current Password" value="{{ old('update_password_current_password') }}">
                    <span class="inline_alert">{{ $errors->updatePassword->first('current_password') }}</span>
                </div>

                <div class="inputs">
                    <label for="update_password_password">New Password</label>
                    <input type="password" name="password" id="update_password_password"
                        placeholder="Enter a long and strong password" value="{{ old('update_password_password') }}">
                    <span class="inline_alert">{{ $errors->updatePassword->first('password') }}</span>
                </div>

                <div class="inputs">
                    <label for="update_password_password_confirmation">Confirm Password</label>
                    <input type="password" name="password_confirmation"
                        id="update_password_password_confirmation" placeholder="Confirm your password"
                        value="{{ old('update_password_password_confirmation') }}">
                    <span
                        class="inline_alert">{{ $errors->updatePassword->first('password_confirmation') }}</span>
                </div>

                <button type="submit">Update</button>
            </form>
        </div>

        @cannot('view-as-super-admin')
            <div class="custom_form">
                <div class="header">
                    <p class="title">Delete Account</p>
                </div>

                <form id="deleteAccountForm" action="{{ route('profile.destroy') }}" method="post">
                    @csrf
                    @method('delete')

                    <p>Please remember that all your data and information will be deleted. Download any data or information you wish to retain.</p>

                    <div class="inputs">
                        <input type="password" id="password" name="password"
                            placeholder="Enter your password to confirm account deletion" required>
                        <span class="inline_alert">{{ $errors->userDeletion->first('password') }}</span>
                    </div>

                    <button id="deleteAccountBtn" type="button" onclick="checkPasswordAndDelete()"
                        class="btn_danger" style="display: none;">Delete Account</button>
                </form>
            </div>
        @endcannot
    </section>

    <x-slot name="scripts">
        <x-sweetalert />
        <script>
            function previewSelectedImage(event) {
                const file = event.target.files[0]; // Get the selected file
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('previewImage').src = e.target.result; // Set the new image preview
                    };
                    reader.readAsDataURL(file);
                }
            }

            function checkPasswordAndDelete() {
                const password = document.getElementById("password").value.trim();

                if (password !== "") {
                    const message = "Are you sure you want to permanently delete your account?";

                    // Show a confirmation dialog
                    showConfirmationDialog(message, () => {
                        // Submit the form if the user confirms
                        const form = document.getElementById("deleteAccountForm");
                        if (form) {
                            form.submit();
                        }
                    });
                }
            }

            // Add an event listener for the input to toggle the button style
            document.getElementById("password").addEventListener('input', function() {
                const password = this.value.trim();
                const deleteAccountBtn = document.getElementById("deleteAccountBtn");

                if (password !== "") {
                    deleteAccountBtn.style.display = "block";
                } else {
                    deleteAccountBtn.style.display = "none";
                }
            });

            // Add an event listener for the Enter key press on the password input
            document.getElementById("password").addEventListener('keypress', function(e) {
                if (e.which === 13) { // 13 is the Enter key code
                    e.preventDefault(); // Prevent default form submission
                    checkPasswordAndDelete();
                }
            });

            // Add an event listener for the Enter key press on the delete button
            document.getElementById("deleteAccountBtn").addEventListener('keypress', function(e) {
                if (e.which === 13) { // 13 is the Enter key code
                    e.preventDefault(); // Prevent default form submission
                    checkPasswordAndDelete();
                }
            });
        </script>
    </x-slot>
</x-authenticated-layout>