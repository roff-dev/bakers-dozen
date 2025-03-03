/* jshint esversion: 8 */
document.addEventListener('DOMContentLoaded', () => {
    // Content for each button card
    const cardContent = {
        bread: {
            title: "established in 2011, our bread is hand-crafted<br>using the finest local ingredients",
            description: `Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore 
            et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut 
            aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse 
            cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa 
            qui officia deserunt mollit anim id est laborum`,
            image: "https://images.pexels.com/photos/30876251/pexels-photo-30876251/free-photo-of-rustic-bread-and-vegetables-on-a-wooden-table.jpeg?auto=compress&cs=tinysrgb&w=600" 
        },
        sweets: {
            title: "handcrafted sweets and pastries<br>made fresh every morning",
            description: `Our delightful selection of sweets and pastries are made fresh daily using traditional recipes
            and the finest ingredients. From cookies to croissants, each item is crafted with care and attention
            to detail to ensure the perfect taste and texture.`,
            image: "https://images.pexels.com/photos/30919066/pexels-photo-30919066/free-photo-of-delicious-chocolate-and-pistachio-covered-croissants.jpeg?auto=compress&cs=tinysrgb&w=600" 
        },
        events: {
            title: "custom cakes and desserts<br>for your special occasions",
            description: `Make your special event even more memorable with our custom-made cakes and desserts.
            We work closely with you to create the perfect dessert that matches your vision and exceeds
            your expectations. From weddings to birthdays, we've got you covered.`,
            image: "https://images.pexels.com/photos/916416/pexels-photo-916416.jpeg?auto=compress&cs=tinysrgb&w=600" 
        }
    };

    // Get all elements
    const foodCards = document.querySelectorAll('.food-card');
    const contentTitle = document.querySelector('.food-information-section h4');
    const contentDescriptions = document.querySelectorAll('.food-description p');
    const contentImage = document.querySelector('.image-circle img');

    // Function to fade out elements
    function fadeOut(elements) {
        if (!Array.isArray(elements)) elements = [elements];
        elements.forEach(element => {
            element.classList.add('fade-out');
            element.classList.remove('fade-in');
        });
    }

    // Function to fade in elements
    function fadeIn(elements) {
        if (!Array.isArray(elements)) elements = [elements];
        elements.forEach(element => {
            element.classList.remove('fade-out');
            element.classList.add('fade-in');
        });
    }

    // Function to update content with fade effect
    async function updateContent(contentKey) {
        // Remove active class from all cards
        foodCards.forEach(card => card.classList.remove('active'));
        
        // Add active class to clicked card
        const activeCard = document.querySelector(`.food-card[data-type="${contentKey}"]`);
        activeCard.classList.add('active');

        // Fade out current content
        fadeOut([contentTitle, ...contentDescriptions, contentImage]);

        // Wait for fade out animation
        await new Promise(resolve => setTimeout(resolve, 300));

        // Update content
        contentTitle.innerHTML = cardContent[contentKey].title;
        contentDescriptions.forEach(p => {
            p.innerHTML = cardContent[contentKey].description;
        });
        contentImage.src = cardContent[contentKey].image;
        contentImage.alt = `${contentKey} image`;

        // Fade in new content
        fadeIn([contentTitle, ...contentDescriptions, contentImage]);
    }

    // Add click handlers to cards
    foodCards.forEach(card => {
        card.addEventListener('click', () => {
            updateContent(card.dataset.type);
        });
    });

    // Set initial state
    contentTitle.classList.add('fade-in');
    contentDescriptions.forEach(p => p.classList.add('fade-in'));
    contentImage.classList.add('fade-in');
    updateContent('bread');
});