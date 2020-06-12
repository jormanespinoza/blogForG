<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Profile;
use App\Entity\Post;
use App\Entity\Comment;
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
        $this->messages = array(
            'I think this article looks generical',
            'Mmm I think the content of this article seems a bit repeated',
            'This article was made with Lorem Ipsum for sure!'
        );
        date_default_timezone_set($this->timezone);
    }

    public function load(ObjectManager $manager)
    {
        // Super Admin
        $superUser = new User();
        $superUser->setUsername('glamit');
        $superUser->setFirstName('Glamit');
        $superUser->setLastName('Argentina');
        $superUser->setEmail('admin@glamit.com.ar');
        $superUser->setRoles(['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN']);
        $superUser->setCreatedAt(new \DateTime());
        $password = $this->encoder->encodePassword($superUser, 'glamit_admin_2020');
        $superUser->setPassword($password);
        $superUser->setIsActive(true);
        $manager->persist($superUser);

        $profile = new Profile();
        $profile->setUser($superUser);
        $manager->persist($profile);

        // Admin
        $adminUser = new User();
        $adminUser->setUsername('admin');
        $adminUser->setFirstName('Admin');
        $adminUser->setLastName('User');
        $adminUser->setEmail('blog.admin@glamit.com.ar');
        $adminUser->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $adminUser->setCreatedAt(new \DateTime());
        $password = $this->encoder->encodePassword($adminUser, 'admin_2020');
        $adminUser->setPassword($password);
        $adminUser->setIsActive(true);
        $manager->persist($adminUser);

        $profile = new Profile();
        $profile->setUser($adminUser);
        $manager->persist($profile);

        // Test User 1
        $testUser1 = new User();
        $testUser1->setUsername('user1');
        $testUser1->setFirstName('First');
        $testUser1->setLastName('User');
        $testUser1->setEmail('user@glamit.com.ar');
        $testUser1->setRoles(['ROLE_USER']);
        $testUser1->setCreatedAt(new \DateTime());
        $password = $this->encoder->encodePassword($testUser1, '123456');
        $testUser1->setPassword($password);
        $testUser1->setIsActive(true);
        $manager->persist($testUser1);

        $profile = new Profile();
        $profile->setUser($testUser1);
        $manager->persist($profile);

        // Test User 2
        $testUser2 = new User();
        $testUser2->setUsername('user2');
        $testUser2->setFirstName('Second');
        $testUser2->setLastName('User');
        $testUser2->setEmail('secondaryuser@glamit.com.ar');
        $testUser2->setRoles(['ROLE_USER']);
        $testUser2->setCreatedAt(new \DateTime());
        $password = $this->encoder->encodePassword($testUser2, '123456');
        $testUser2->setPassword($password);
        $testUser2->setIsActive(true);
        $manager->persist($testUser2);

        $profile = new Profile();
        $profile->setUser($testUser2);
        $manager->persist($profile);

        // Creating example posts
        for ($i = 1; $i <= $this->postsQty; $i++) {
            $post = new Post();
            $post->setTitle("Post {$i}");
            $post->setBody($this->lorem);
            $post->setUrl("post-{$i}");
            $post->setRejected(false);
            $post->setCreatedAt(new \DateTime());
            // Assign a user as creator of the post
            // Odd for testUser1 / Even for textUser2
            $creator = $i % 2 !== 0 ? $testUser1 : $testUser2;
            $post->setUser($creator);
            $post->setVisible(true);
            $manager->persist($post);

            // Addding a random comment to this post
            $comment = new Comment();
            $creator = $i % 2 === 0 ? $testUser1 : $testUser2; // Reassigns the comment creator
            $comment->setUser($creator);
            $comment->setPost($post);
            // Set the random message
            $message = $this->messages[random_int(0, 2)];
            $comment->setMessage($message);
            $comment->setCreatedAt(new \DateTime());
            $manager->persist($comment);
        }

        $manager->flush();
    }
}
