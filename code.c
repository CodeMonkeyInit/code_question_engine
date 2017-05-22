#include <stdio.h>
 
typedef struct MyList
{
    int d;
    MyList* pNext;
} MYLIST;
 
MYLIST* pList = NULL;
MYLIST* pListTail = NULL;
 
void AddHead(int d);
void AddTail(int d);
int Find(int i);
void ShowList();
 
int main(void)
{
    AddHead(10);
    AddTail(15);
    AddHead(20);
    AddTail(16);
    AddHead(30);
    AddTail(17);
    AddHead(40);
    AddTail(18);
 
    ShowList();
 
    printf("found = %d\n",Find(4));
 
   
 
    return 0;
}
 
void AddHead(int d)
{
    MYLIST* pListItem = new MYLIST;
    pListItem->d = d;
    if (pList != NULL) pListItem->pNext = pList;
    else { pListTail = pListItem; pListItem->pNext = NULL; }
    pList = pListItem;
}
 
void AddTail(int d)
{
    MYLIST* pListItem = new MYLIST;
    pListItem->d = d;
    pListItem->pNext = NULL;
    if (pList == NULL) 
        { pListTail = pListItem; pList = pListTail; }
    else { pListTail->pNext = pListItem; pListTail = pListItem; }
}
 
int Find(int i)
{
    int n = 0, ret = -1;
    MYLIST* pMyList = pList;
    while (pMyList != NULL)
    {
        if (n == i) ret = pMyList->d;
        pMyList = pMyList->pNext; n++;
    }
 
    return ret;
}
 
void ShowList()
{
    for (MYLIST* pMyList = pList; pMyList != NULL; 
        pMyList = pMyList->pNext)
        printf("%d\n",pMyList->d);
}
