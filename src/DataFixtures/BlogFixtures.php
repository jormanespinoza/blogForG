<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\{User, Post};
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class BlogFixtures extends Fixture
{
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->timezone = 'America/Argentina/Buenos_Aires';
        $this->postsQty = 12;
        $this->lorem = '
            <p>Mollis semper feugiat natoque elit. Volutpat ultrices pharetra aliquet sagittis imperdiet velit interdum sed nibh. Fermentum faucibus fermentum non sociosqu. Ad felis vivamus diam sodales nibh dictumst cras primis tincidunt. Scelerisque purus ultricies hac consequat duis varius! Malesuada nullam eleifend diam euismod massa libero laoreet habitant aliquam torquent. Ante egestas auctor praesent. Eu dapibus arcu luctus!</p>
            <p>Libero phasellus integer netus, senectus per eros scelerisque. Orci dis eros venenatis curae; lorem rhoncus rhoncus, suspendisse velit ipsum enim netus! Penatibus ornare maecenas iaculis tempor enim suscipit mollis bibendum. Arcu cursus duis non. Mauris pulvinar posuere orci himenaeos rhoncus sagittis massa sapien risus. Eleifend per urna sodales eu viverra laoreet turpis pharetra. Nascetur placerat quam suspendisse orci pellentesque vestibulum sociosqu dictumst amet venenatis dignissim. Dictum blandit tristique sollicitudin aliquet integer sit vel senectus justo fringilla quam porta. Ante accumsan sit quisque elit, varius proin.</p>
        ';
    }

    public function load(ObjectManager $manager)
    {
        date_default_timezone_set($this->timezone);

        // Admin
        $user = new User();
        $user->setUsername('glamit');
        $user->setFirstName('Glamit');
        $user->setLastName('Argentina');
        $user->setEmail('blog.admin@glamit.com.ar');
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $user->setCreatedAt(new \DateTime());
        $password = $this->encoder->encodePassword($user, 'glamit_admin_2020');
        $user->setPassword($password);
        $manager->persist($user);

        // Test User 1
        $testUser1 = new User();
        $testUser1->setUsername('testuser1');
        $testUser1->setFirstName('First');
        $testUser1->setLastName('User');
        $testUser1->setEmail('firstuser@glamit.com.ar');
        $testUser1->setRoles(['ROLE_USER']);
        $testUser1->setCreatedAt(new \DateTime());
        $password = $this->encoder->encodePassword($testUser1, 'user1_2020');
        $testUser1->setPassword($password);
        $manager->persist($testUser1);

        // Test User 2
        $testUser2 = new User();
        $testUser2->setUsername('testuser2');
        $testUser2->setFirstName('Second');
        $testUser2->setLastName('User');
        $testUser2->setEmail('seconduser@glamit.com.ar');
        $testUser2->setRoles(['ROLE_USER']);
        $testUser2->setCreatedAt(new \DateTime());
        $password = $this->encoder->encodePassword($testUser2, 'user2_2020');
        $testUser2->setPassword($password);
        $manager->persist($testUser2);

        // Creating example posts
        for ($i = 1; $i <= $this->postsQty; $i++) {
            $post = new Post();
            $post->setTitle("Post {$i}");
            $post->setBody($this->lorem);
            $post->setUrl("post-{$i}");
            $post->setRejected(FALSE);
            $post->setCreatedAt(new \DateTime());
            // Assign a user as creator of the post
            if ($i % 2 !== 0) {
                $post->setUser($testUser1);
            } else {
                $post->setUser($testUser2);
            }
            $manager->persist($post);
        }

        $manager->flush();
    }
}
