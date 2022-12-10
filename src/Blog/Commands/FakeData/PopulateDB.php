<?php

namespace Granal1\Php2\Blog\Commands\FakeData;

use Granal1\Php2\Blog\Comment;
use Granal1\Php2\Blog\Post;
use Granal1\Php2\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Granal1\Php2\Blog\Repositories\UsersRepository\UserRepositoryInterface;
use Granal1\Php2\Blog\Repositories\CommentRepository\CommentRepositoryInterface;
use Granal1\Php2\Blog\User;
use Granal1\Php2\Blog\UUID;
use Granal1\Php2\Person\Name;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDB extends Command
{
    //Внедряем генератор тестовых данных,
    //репозитории пользователей и статей
    public function __construct(
        private \Faker\Generator $faker,
        private UserRepositoryInterface $usersRepository,
        private PostRepositoryInterface $postsRepository,
        private CommentRepositoryInterface $commentRepository,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
        ->setName('fake-data:populate-db')
        ->setDescription('Populates DB with fake data')
        ->addArgument('users-number', InputArgument::OPTIONAL, 'Number of Users', 1)
        ->addArgument('posts-number', InputArgument::OPTIONAL, 'Number of Posts', 1)
        ->addArgument('comments-number', InputArgument::OPTIONAL, 'Number of Comments from each User', 1);
    }

    protected function execute(
        InputInterface $input, 
        OutputInterface $output
    ): int
    {
        //Создаем пользователей
        $users = [];
        for ($i=0; $i < $input->getArgument('users-number'); $i++) { 
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->username());
        }

        //От имени каждого пользователя
        //создаем заданное количество статей
        $posts = [];
        foreach ($users as $user) {
            for ($i=0; $i < $input->getArgument('posts-number'); $i++) { 
                $post = $this->createFakePost($user);
                $posts[] = $post;
                $output->writeln('Post created: ' . $post->getTitle());
            }
        }

        //Под каждой статьей создается заданное количество комментариев 
        //от каждого пользователя
        foreach ($posts as $post) {
            foreach ($users as $user) {
                for ($i=0; $i < $input->getArgument('comments-number'); $i++) { 
                    $comment = $this->createFakeComment($user, $post);
                    $output->writeln('Comment created: ' . $comment->getText());
                }
            }
        }

        return Command::SUCCESS;
    }

    private function createFakeUser(): User
    {
        $gender = $this->faker->randomElement(['male','female']);
        $user = User::createFrom(
            new Name(
                $this->faker->firstName($gender),
                $this->faker->lastName($gender)
            ),
            $this->faker->userName($gender),
            $this->faker->password()
        );

        $this->usersRepository->save($user);
        return $user;
    }

    private function createFakePost(User $author): Post
    {
        $post = new Post(
            UUID::random(),
            $author,
            $this->faker->sentence(6, true),
            $this->faker->realText()
        );

        $this->postsRepository->save($post);
        return $post;
    }

    private function createFakeComment(User $user, Post $post): Comment
    {
        $comment = new Comment(
            UUID::random(),
            $post,
            $user,
            $this->faker->realText()
        );

        $this->commentRepository->save($comment);
        return $comment;
    }

/*
TODO 
Гендер имени работает.
Разобраться в создании осознанного логина с учетом имени и гендера
Разобраться с формированием заголовка статьи на русском
https://fakerphp.github.io/

*/

}